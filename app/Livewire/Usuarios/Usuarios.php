<?php

namespace App\Livewire\Usuarios;

use App\Models\Area;
use App\Models\Cargo;
use Livewire\Component;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManagerStatic as Image;

class Usuarios extends Component
{
    use WithFileUploads;

    public $users = [];
    public $roles;
    public $user_id, $status, $document, $name, $last_name, $email, $phone, $user_name, $password, $picture, $role_id, $change_picture = false;
    public $reset_password;
    public $areas;
    public $cargos;
    public $area_id;
    public $cargo_id;

    public function mount()
    {
        $this->roles = Role::all();
        $this->areas = Area::orderBy('nombre')->get();
        $this->cargos = Cargo::orderBy('nombre')->get();
    }

    public function selectedRole($role_id)
    {
        $this->role_id = $role_id;
    }

    public function getUsers()
    {
        $this->skipRender();
        // return User::with('roles')->get();
        return User::with(['roles', 'area', 'cargo'])->get();
    }

    public function save()
    {

        if ($this->user_id) {
            $required_pass = 'nullable';
        } else {
            $required_pass = 'required|min:8';
        }

        $this->validate([
            'document'  => 'required',
            'name'      => 'required',
            'last_name' => 'required',
            'user_name' => 'required|min:1',
            'password'  => $required_pass,
            'email'     => 'nullable|email|unique:users,email,' . $this->user_id,
            'role_id'   => 'required',
            'area_id'   => 'required',
            'cargo_id'  => 'required',
        ]);
        //'password'  => $this->user_id
        //    ? 'nullable|min:8|regex:/[A-Z]/|regex:/[a-z]/|regex:/[0-9]/|regex:/[\W_]/'
        //   : 'required|min:8|regex:/[A-Z]/|regex:/[a-z]/|regex:/[0-9]/|regex:/[\W_]/',

        $role = Role::findById($this->role_id);

        // Procesar la imagen si se cambió
        if ($this->picture && $this->change_picture) {
            $name_picture = $this->processImage($this->picture);
        } else {
            $name_picture = $this->user_id ? User::find($this->user_id)->picture : 'default.png';
        }

        if ($this->user_id) {
            $user = User::find($this->user_id);
            if ($user) {
                if ($this->change_picture && $user->picture) {
                    Storage::disk('public')->delete('avatars/' . $user->picture);
                }
                $dataToUpdate = [
                    'document'  => $this->document,
                    'name'      => $this->name,
                    'last_name' => $this->last_name,
                    'user_name' => trim($this->user_name),
                    'email'     => $this->email,
                    'phone'     => $this->phone,
                    'status'    => $this->status ?? 1,
                    'picture'   => $name_picture ?? $user->picture,
                    'area_id'   => $this->area_id,
                    'cargo_id'  => $this->cargo_id,
                ];

                // Solo actualizar la contraseña si se ha proporcionado una nueva
                if (!empty($this->reset_password)) {
                    $dataToUpdate['password'] = bcrypt(trim($this->reset_password));
                }

                $user->update($dataToUpdate);

                $user->syncRoles($role);
            }
        } else {
            $user = User::create([
                'document'  => $this->document,
                'name'      => $this->name,
                'last_name' => $this->last_name,
                'user_name' => trim($this->user_name),
                'password'  => bcrypt(trim($this->password)),
                'email'     => $this->email,
                'phone'     => $this->phone,
                'picture'   => $name_picture,
                'status'    => 1,
                'area_id'   => $this->area_id,
                'cargo_id'  => $this->cargo_id,
            ]);

            $user->assignRole($role);
        }

        if ($user) {
            $user->load(['roles']);
            $this->reset();
            $this->mount();
            return $user->toArray();
        } else {
            return false;
        }
    }

    private function processImage($base64_image)
    {
        $image_parts = explode(";base64,", $base64_image);
        $image_type_aux = explode("image/", $image_parts[0]);
        $ext = $image_type_aux[1];
        $imagen = base64_decode($image_parts[1]);
        $rand = date('Ymdhs') . Str::random(5);
        $name_picture = 'avatar-' . $rand . '.' . $ext;
        $img = Image::make($imagen)->widen(100)->encode($ext);
        Storage::disk('public')->put('avatars/' . $name_picture, $img);

        return $name_picture;
    }

    public function desactivarUser($user_id)
    {
        $user = User::find($user_id);
        if ($user) {
            $user->update(['status' => 0]);
            $user->load(['roles']);
            $this->reset();
            $this->mount();
            return $user->toArray();
        } else {
            return false;
        }
    }

    public function limpiar()
    {
        $this->reset();
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.usuarios.usuarios')->title('Personal');
    }
}
