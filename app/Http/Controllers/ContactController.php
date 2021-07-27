<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $kontak = Contact::all();
        return view('send', ['kontak' => $kontak]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $kontak = Contact::where('id', $id)->first();
        $headers = array(
            // 'x-api-key:CAT-Riset-UNM----'
        );

        $fields = [
            'number' =>  $kontak['nomor'],
            'message' => $kontak['pesan'],

        ];

        $api_path = "http://wa.api.unm.ac.id/send-message";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_path);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $featuredJobs = curl_exec($ch);

        if (curl_errno($ch)) {
            echo 'Curl error: ' . curl_error($ch);
            return response()->json([
                'message' => 'Gagal login'
            ], 422);
        } else {
            // check the HTTP status code of the request
            $resultStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $result = (array)json_decode($featuredJobs);
            if ($resultStatus != 200) {
                Contact::where('id', $kontak['id'])
                    ->update([
                        'status' => 2,
                    ]);

                return redirect('send-wa');
            }
            Contact::where('id', $kontak['id'])
                ->update([
                    'status' => 1,
                ]);

            return redirect('send-wa');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
