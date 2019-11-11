<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Auth;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OrdersController extends Controller
{
    protected $label_ids = [
        'external' => '5ac3b3a17566aef831377381',
        'internal' => '5ac3b38b88b3d5713fc4a8ee',
        'badge' => '58979ad58990c65a08ddae82',
        'shirt' => '58979add7feac84ca924de04',
        'jumper' => '58979add7feac84ca924de04'
    ];

    protected $allowed_extensions = [
        'jpg','jpeg','png','gif'
    ];

    protected $order_types = [
        'badge' => 1,
        'shirt' => 2,
        'jumper' => 3
    ];

    public function create(Request $request)
    {
        return view('orders.new');
    }

    public function trelloCardDescription($name,$email,$time_limit,$count,$size,$font,$comment,$image)
    {
        return "
# Adatok

*Rendelésből kinyert:*

 - **Rendelő:** $name ($email)
 - **Határidő:** $time_limit
 - **Darabszám:** $count
 - **Fullextrás betűtípus:** $font
 - **Megjegyzés:** $comment
 - **Kép:** $image

*Kitöltendő:*

 - **Öltésszám:** 
 - **Ár:** $count x {Darabár} = ...HUF
 - **Terv fájl helye:** 
 - **Felhasznált cérnák azonosítói:** 
 - **Felhasznált PTP neve:** 

---

# Leírás

$size cm oldalhosszúság

---

# Aktuális információk
";
    }

    public function trello(Order $order)
    {
        $curl = curl_init();

        $key = env('TRELLO_ID');
        $token = env('TRELLO_KEY');
        $list = env('TRELLO_LIST');

        $labels = "";
        if($order->type==1){
            $labels .= $this->label_ids['badge'].',';
        }else{
            $labels .= $this->label_ids['shirt'].',';
        }

        if($order->internal==true){
            $labels .= $this->label_ids['internal'];
        }else{
            $labels .= $this->label_ids['external'];
        }

        $font = $order->font==null ? 'nincs' : $order->font;
        $comment = $order->comment == null ? 'nincs' : $order->comment;

        $image = route('orders.getImage', ['order' => $order]);

        $data = [
            'name' => $order->title,
            'desc' => $this->trelloCardDescription($order->user->name,$order->user->email,$order->time_limit,$order->count,$order->size,$font,$comment,$image),
            'pos' => 'top',
            'idList' => $list,
            'idLabels' => $labels
        ];

        $data = json_encode($data);

        $url = "https://api.trello.com/1/cards?key=$key&token=$token";

        curl_setopt($curl, CURLOPT_URL,$url);
        curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($curl, CURLOPT_POST,true);
        curl_setopt($curl,CURLOPT_POSTFIELDS,$data);

        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data))
        );

        $output = json_decode(curl_exec($curl));

        $this->trelloChecklist($output->id);


        curl_close($curl);
    }

    public function save(Request $request)
    {
        if($request->file('image')){
            $file = $request->file('image');
            $extension = strtolower($file->extension());
            $size = $file->getSize()/1024/1024;

            if($size>10){
                return redirect()->back();
            }

            if(!in_array($extension,$this->allowed_extensions)){
                return redirect()->back();
            }

            $new_name = 'images/uploads/' . time().$request->input('name').'.'.$file->extension();

            Storage::disk()->put($new_name, File::get($file));

            $title = $request->input('title');
            $count = $request->input('count');
            $time_limit = date("Y-m-d",strtotime($request->input('time_limit')));
            $type = $request->input('order_type');
            $size = $request->input('size');
            $font = $request->input('font');
            $internal = $request->input('internal')=='internal';
            $comment = $request->input('comment');

            $order = new Order();
            $order->title = $title;
            $order->count = $count;
            $order->time_limit = $time_limit=="" ? null : $time_limit;
            if(array_key_exists($type, $this->order_types)){
                $order->type = $this->order_types[$type];
            }else{
                $order->type = 1;
            }
            $order->size = $size;
            $order->font = $font=="" ? null : $font;
            $order->internal = $internal;
            $order->comment = $comment=="" ? null : $comment;
            $order->image = $new_name;
            $order->user_id = Auth::id();
            $order->save();

            return redirect()->route('user.orders');
        }else{
            return redirect()->back();
        }
    }

    public function unapproved()
    {
        if(Auth::user()->role_id<2)
        {
            abort(403);
        }

        $orders = Order::where('approved_by',null)->get();

        return view('orders.unapproved',[
            'orders' => $orders
        ]);
    }

    public function approve(Order $order)
    {
        if(Auth::user()->role_id<2)
        {
            abort(403);
        }

        $order->approved_by = Auth::id();
        $order->save();

        $this->trello($order);

        return redirect()->back();
    }

    public function getImage(Order $order)
    {
        $path = $order->image;
        return response()->file(storage_path("app/" . $path));
    }

    public function trelloChecklist($card)
    {
        $curl = curl_init();

        $key = env('TRELLO_ID');
        $token = env('TRELLO_KEY');

        $data = [
            'idCard' => $card,
            'name' => 'Teendők',
            'pos' => 'bottom',
            'idChecklistSource' => '5da4934464eda41a074e64f5'
        ];


        $url = "https://api.trello.com/1/checklists?idCard=$card&key=$key&token=$token";

        curl_setopt($curl, CURLOPT_URL,$url);
        curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($curl, CURLOPT_POST,true);
        curl_setopt($curl, CURLOPT_POSTFIELDS,$data);

        $output = json_decode(curl_exec($curl));
    }

    public function testTrello()
    {
        $curl = curl_init();

        $key = env('TRELLO_ID');
        $token = env('TRELLO_KEY');
        $list = env('TRELLO_LIST');


        $url = "https://api.trello.com/1/lists/$list/cards?key=$key&token=$token";

        curl_setopt($curl, CURLOPT_URL,$url);
        curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);

        $output = json_decode(curl_exec($curl));

        dd($output);
    }
}
