<?php

namespace App\Http\Livewire\Admin\Users;

use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;

class ListUsers extends Component
{
    // public $name;
    // public $email;
    // public $password;
    // public $password_confirmation;

    public $state = [];
    public $deleteId = [];


    public $user;

    public $showEditModal = false;

    public $userIdBeingRemoved = null;

    public function addNewUser()
    {
        $this->state = [];
        $this->showEditModal = false;
        $this->dispatchBrowserEvent('show-form');
    }

    public function createUser()
    {
        $validatedData = Validator::make($this->state,[
            'name' =>'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed',
        ])->validate();

        $validatedData['password'] = bcrypt($validatedData['password']);

        User::create($validatedData);
        // session()->flash('message','User Added Successfully!');
        $this->dispatchBrowserEvent('hide-form',['message' => 'User Added Successfully!']);
        return redirect()->back();
    }

    public function edit(User $user)
    {
        $this->showEditModal = true;
        $this->user = $user;
        // dd($user->toArray());
        $this->state = $user->toArray();
        $this->dispatchBrowserEvent('show-form');
    }

    public function updateUser()
    {
        $validatedData = Validator::make($this->state,[
            'name' =>'required',
            'email' => 'required|email|unique:users,email,'.$this->user->id,
            'password' => 'sometimes|confirmed',
        ])->validate();

        if(!empty($validatedData['password'])){
            $validatedData['password'] = bcrypt($validatedData['password']);
        }


        $this->user->update($validatedData);

        // session()->flash('message','User Added Successfully!');
        $this->dispatchBrowserEvent('hide-form',['message' => 'User Updated Successfully!']);
    }

    public function confirmUserRemoval($userId)
    {
        $this->userIdBeingRemoved = $userId;
        // dd($userId);
        $this->dispatchBrowserEvent('show-delete-modal');
    }

    public function deleteUser()
    {
        $user = User::find($this->userIdBeingRemoved);
        // dd($user);
        $user->delete();
        $this->dispatchBrowserEvent('hide-delete-modal',['message' => 'User Deleted Successfully!']);
    }

    public function render()
    {
        $users = User::latest()->paginate();
        return view('livewire.admin.users.list-users',[
            'users' => $users,
        ]);
    }
}
