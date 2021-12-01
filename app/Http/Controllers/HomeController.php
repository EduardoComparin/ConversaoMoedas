<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Historico;
use Illuminate\Support\Facades\Mail;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('dashboard');
    }

    public function save(Request $request)
    {
        $usuario = auth()->user();

       
        $id_usuario = $usuario->id;
        $json = json_encode($request->all());

      
        Historico::create([
            'users_id' => $id_usuario,
            'cotacao' =>  $json
        ]);


       $mail =  Mail::send('mail.teste', ['cotacao' => $request->all()], function ($m) use ($usuario) {
            $m->from('senderteste2@gmail.com', 'Eduardo Comparin');
            $m->to($usuario->email, $usuario->name);
            $m->subject('Cotação de valores');
        });


        return response()->json(['success'=>'sucesso']);
    }


    public function historicoCotacao(){
        $id_usuario = auth()->user()->id;
        $historicoUsers =  Historico::select('cotacao')->where('users_id',$id_usuario)->get()->toArray();

        $retorno = array();
        foreach ($historicoUsers as $historico) {
            $retorno[] = json_decode($historico['cotacao'],true);
        }
        
        return view('pages.table_list') ->with('cotacoes', $retorno);
    }


    
}
