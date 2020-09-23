<?php

namespace App\Http\Controllers;

use App\UserPhpFox;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class UserPhpFoxController extends Controller
{
    public function getToken(){
        $token = new UserPhpFox();
        $token = $token->getAuthorization();
        return $token;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\UserPhpFox  $userPhpFox
     * @return \Illuminate\Http\Response
     */
    public function show(UserPhpFox $userPhpFox)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\UserPhpFox  $userPhpFox
     * @return \Illuminate\Http\Response
     */
    public function edit(UserPhpFox $userPhpFox)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\UserPhpFox  $userPhpFox
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, UserPhpFox $userPhpFox)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\UserPhpFox  $userPhpFox
     * @return \Illuminate\Http\Response
     */
    public function destroy(UserPhpFox $userPhpFox)
    {
        //
    }
}
