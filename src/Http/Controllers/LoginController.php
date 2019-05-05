<?php

namespace Encore\Login\Http\Controllers;

use App\Admin\Models\AdminUser;
use App\Admin\Models\Assistant;
use App\Admin\Models\Brand;
use App\Admin\Models\Employee;
use App\Admin\Models\Event;
use App\Admin\Models\Team;
use Encore\Admin\Facades\Admin;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    public function login()
    {
        if ($this->guard()->check()) {
            return redirect($this->redirectPath());
        }

        $cookie = \Cookie::forget('user', '/admin');

        return response()->view('login::index')
            ->withCookie($cookie);
    }

    public function postLogin(Request $request)
    {
        $credentials = $request->only(['username', 'password']);
        $validator = Validator::make($credentials, [
            'username' => 'required',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return Redirect::back()->withInput()->withErrors($validator);
        }

        if (Auth::guard('admin')->attempt($credentials)) {

            admin_toastr(trans('admin.login_successful'));

            if (Admin::user()->inRoles(['super', 'administrator'])) {
                return redirect()->intended(config('admin.route.prefix'));
            } else {
                $user = $this->getUserInfo($request);
                $cookies = [
                    \Cookie('user', $user, 24 * 60 * 60, '/admin'),
                ];

                return redirect()->intended(config('admin.route.prefix'))
                    ->withCookies($cookies);
            }
        }

        return Redirect::back()->withInput()->withErrors([
            'username' => $this->getFailedLoginMessage()
        ]);
    }

    /**
     * Get the post login redirect path.
     *
     * @return string
     */
    protected function redirectPath()
    {
        if (method_exists($this, 'redirectTo')) {
            return $this->redirectTo();
        }

        return property_exists($this, 'redirectTo') ? $this->redirectTo : config('admin.route.prefix');
    }

    /**
     * Send the response after the user was authenticated.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    protected function sendLoginResponse(Request $request)
    {
        admin_toastr(trans('admin.login_successful'));

        $request->session()->regenerate();

        return redirect()->intended($this->redirectPath());
    }

    /**
     * @return string|\Symfony\Component\Translation\TranslatorInterface
     */
    protected function getFailedLoginMessage()
    {
        return Lang::has('auth.failed')
            ? trans('auth.failed')
            : 'These credentials do not match our records.';
    }

    /**
     * 督导以下用户登陆都需要
     * 获取event_id,brand_id
     * @param $request
     * @return string
     */
    protected function getUserInfo($request): string
    {
        $username = $request->input('username');

        $current = '';
        $team_id = '';
        $event_id = '';
        $brand_id = '';
        $employee_id = '';
        $team_name = '';

        $admin_user = AdminUser::where('username', '=', $username)->get();
        if (!$admin_user->isEmpty() && isset($admin_user[0]->id)) {
            $id = $admin_user[0]->id;

            if (Admin::user()->isRole('event')) {
                $current = 'event';
                $dt = date('Y-m-d');
                $event = Event::where('user_id', '=', $id)
                    ->where('start_date', '<', $dt)
                    ->where('end_date', '>', $dt)
                    ->get();
                if (!$event->isEmpty()) {
                    $team_id = $event[0]->team_id;
                    $event_id = $event[0]->id;
                } else {
                    // 只有浏览权限
                    $event_id = 0;
                }
            }

            if (Admin::user()->isRole('brand')) {
                $current = 'brand';
                $brand = Brand::where('user_id', '=', $id)
                    ->get();
                if (!$brand->isEmpty()) {
                    $event_id = $brand[0]->event_id;
                    $brand_id = $brand[0]->id;
                }
            }

            if (Admin::user()->isRole('employee')) {
                $current = 'employee';
                $employee = Employee::where('user_id', '=', $id)
                    ->get();
                if (!$employee->isEmpty()) {
                    $event_id = $employee[0]->event_id;
                    $brand_id = $employee[0]->brand_id;
                    $employee_id = $employee[0]->id;
                }
            }

            if (Admin::user()->inRoles(['assistant-checker', 'assistant-order'])) {
                $current = 'assistant';
                $assistant = Assistant::where('user_id', '=', $id)
                    ->get();
                if (!$assistant->isEmpty()) {
                    $event_id = $assistant[0]->event_id;
                }
            }

            if (Admin::user()->isRole('team')) {
                $current = 'team';
                $team = Team::where('user_id', '=', $id)
                    ->with('user')
                    ->get();
                if (!$team->isEmpty()) {
                    $team_id = $team[0]->id;
                    $team_name = $team[0]->user()->name;
                }
            } else {
                if ($event_id && $event_id > 0) {
                    $team = Team::join('prefix_event', 'prefix_team.id', 'prefix_event.team_id')
                        ->select('prefix_team.*')
                        ->with('user')
                        ->where('prefix_event.id', '=', $event_id)
                        ->get();

                    if (!$team->isEmpty()) {
                        $team_name = $team[0]->user->name;
                    }

                }
            }

        }

        $user = [
            'current' => $current,
            'team_id' => $team_id,
            'team_name' => $team_name,
            'event_id' => $event_id,
            'brand_id' => $brand_id,
            'employee_id' => $employee_id,
        ];
        
        return base64_encode(json_encode($user));
    }

    protected function guard()
    {
        return Auth::guard('admin');
    }
}
