<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\User;
use App\Models\UserAppointment;
use App\Models\UserFavorite;
use App\Models\Barber;
use App\Models\BarberPhotos;
use App\Models\BarberServices;
use App\Models\BarberTestimonial;
use App\Models\BarberAvailability;


class BarberController extends Controller
{
    private $loggedUser;

    public function __construct()
    {
        $this->middleware('auth:api');
        $this->loggedUser = auth()->user();
    }

    public function createRandom()
    {
        $array = ['error'=>''];

        return $array;
    }

    public function list(Request $request)
    {
        $array = ['error'=>''];

        $barbers = Barber::all();

        foreach ($barbers as $bkey => $bvalue) {
            $barbers[$bkey]['avatar'] = url('media/avatars/'.$barbers[$bkey]['avatar']);
        }

        $array['data'] = $barbers;
        $array['loc'] = 'São Paulo';

        return $array;
    }

    public function one($id)
    {
        $array = ['error'=>''];

        $barber = Barber::find($id);

        if ($barber) {
            $barber['avatar'] = url('media/avatars/'.$barber['avatar']);
            $barber['favorited'] = false;
            $barber['photos'] = [];
            $barber['services'] = [];
            $barber['testmonials'] = [];
            $barber['available'] = [];

            $cFavorite = UserFavorite::where('id_user', $this->loggedUser->id)->where('id_barber', $barber->id)->count();
            if ($cFavorite > 0) {
                $barber['favoreited'] = true;
            }

            $barber['photos'] = BarberPhotos::select(['id', 'url'])::where('id_barber', $barber->id)->get();
            foreach ($barber['photos'] as $bpkey => $bpvalue) {
                $barbers['photos'][$bpkey]['url'] = url('media/uploads/'.$barbers['photos'][$bpkey]['url']);
            }

            $barber['services'] = BarberServices::select(['id', 'name', 'price'])->where('id_barber', $barber->id)->get();

            $barber['testimonials'] = BarberTestimonial::select(['id', 'name', 'rate', 'body'])->where('id_barber', $barber->id)->get();

            $availability = [];

            $avails = BarberAvailability::where('id_barber', $barber->id)->get();
            $availWeekdays = [];
            foreach ($avails as $item) {
                $availWeekdays[$item['weekday']] = explode(',', $item['hours']);
            }

            $appointments = [];
            $appQuery = UserAppointment::where('id_barber', $barber->id)->whereBetween('ap_datetime', [
                date('Y-m-d').' 00:00:00',
                date('Y-m-d', strtotime('+20 days')).' 23:59:59'
            ])->get();

            foreach ($appQuery as $appItem) {
                $appointments[] = $appItem['ap_datetime'];
            }

            for ($q=0; $q<20; $q++) {
                $timeItem = strtotime('+'.$q. 'days');
                $weekday = date('w', $timeItem);

                if (in_array($weekday, array_keys($availWeekdays))) {
                    $hours = [];

                    $dayItem = date('Y-m-d', $timeItem);

                    foreach ($availWeekdays[$weekday] as $hourItem) {
                        $dayFormated = $dayItem.' '.$hourItem.':00';
                        if (in_array($dayFormated, $appointments)) {
                            $hours[] = $hourItem;
                        }
                    }

                    if (count($hours) > 0) {
                        $availability[] = [
                            'date' => $dayItem,
                            'hours' => $hours
                        ];
                    }

                }
            }

            $barber['available'] = $availability;

            $array['data'] = $barber;
        } else {
            $array['error'] = 'Barbeiro não existe';
            return $array;
        }

        return $array;
    }

    public function setAppointment($id, Request $request)
    {
        $array = ['error'=>''];

        $service = $request->input('service');
        $year = intval($request->input('year'));
        $month = intval($request->input('month'));
        $day = intval($request->input('day'));
        $hour = intval($request->input('hour'));

        $month = ($month < 10) ? '0'.$month : $month;
        $day = ($day < 10) ? '0'.$day : $day;
        $hour = ($hour < 10) ? '0'.$hour : $hour;

        $barberservice = BarberServices::select()->where('id', $service)->where('id_barber', $id)->first();

        if ($barberservice) {
            $apDate = $year.'-'.$month.'-'.$day.' '.$hour.':00:00';
            if (strtotime($apDate > 0)) {
                $apps = UserAppointment::select()->where('id_barber', $id)->where('ap_datetime', $apDate)->count();
                if ($apps === 0) {
                    $weekday = date('w', strtotime($apDate));
                    $avail = BarberAvailability::select()->where('id_barber', $id)->where('weekday', $weekday)->first();
                    if ($avail) {
                        $hours = explode(',', $avail['hours']);
                        if (in_array($hour.':00', $hours)) {
                            $newApp = new UserAppointment();
                            $newApp->id_user = $this->loggedUser->id;
                            $newApp->id_barber = $id;
                            $newApp->id_service = $service;
                            $newApp->ap_datetime = $apDate;
                            $newApp->save();

                        } else {
                            $array['error'] = 'Barbeiro não atende nesta hora';
                        }

                    } else {
                        $array['error'] = 'Barbeiro não atende neste dia';
                    }

                } else {
                    $array['error'] = 'Barbeiro já possui agendamento neste dia/hora';
                }


            } else {
                $array['error'] = 'Data inválida!';
            }


        } else {
            $array['error'] = 'Serviço inexistente!';
        }

        return $array;
    }
}
