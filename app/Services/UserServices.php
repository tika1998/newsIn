<?php

namespace App\Services;
use Illuminate\Http\Request;
use App\Contract\UserInterface;
use App\Http\Requests\AdminCreate;
use App\Mail\AdminMail;
use App\Mail\AdminSuccess;
use App\Mail\VerifyMail;
use App\User;
use App\VerifyUser;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class UserServices implements UserInterface
{
    public function index()
    {
        // TODO: Implement index() method.
        $users = User::all();
        return $users;
    }

    public function changeStatus($id)
    {
        // TODO: Implement changeStatus() method.

        $user = User::find($id);
        $email = $user->email;
        $user->update(['status' => 'success']);
        Mail::to($email)->send(new AdminSuccess());
        return back();
    }

    public function blockUSer($id) {
        $user = User::find($id);
        if ($user->status !== 'block') {
            $user->update(['status' => 'block']);
        } else {
            $user->update(['status' => 'success']);
        }
        return back();
    }

    public function create()
    {
        // TODO: Implement create() method.
        return view('admin.users.create');
    }

    public function store(AdminCreate $request)
    {
        // TODO: Implement store() method.

        $pass = $request->input('password');
        $hashPass = Hash::make($pass);
        $userAdm = $request->all();
        $userAdm['password'] = $hashPass;
        $user = User::create($userAdm);

        $verifyUser = VerifyUser::create([
            'user_id' => $user->id,
            'token' => sha1($user->email)
        ]);
        Mail::to($user->email)->send(new VerifyMail($user, $pass));

        return back();
    }

    public function verifyUser($token)
    {
        $verifyUser = VerifyUser::where('token', $token)->first();
        if($verifyUser != null){
            $user = $verifyUser->user;
            if(!$user->verified) {
                $verifyUser->user->verified = 1;
                $verifyUser->user->save();
                $text = "duq ancaq verifikacian";
            } else {
                $text = "dzer emaile arden ancel  e verifikcia";
            }
        } else {
            return redirect('/')->with('error', "soooo Sorry");
        }

        return redirect('/')->with('message', $text);
    }

    public function edit($id)
    {
        // TODO: Implement edit() method.
        $user = User::find($id);

        if ($user) {
            return view('admin.users.updateUser', compact('user'));
        } else {
            return back();
        }
    }

    public function update(Request $request, $id)
    {
        // TODO: Implement update() method.
        $user = User::find($id);
        if ($user) {
            $user->update($request->all());
            return redirect('user')->with('message', 'edit successfully');
        } else {
            return back();
        }
    }

    public function destroy($id)
    {
        // TODO: Implement destroy() method.
        $user = User::find($id);
        //dd($user);
        if ($user) {
            $user->delete();
            return redirect('/user');
        } else {
            return back();
        }
    }

}