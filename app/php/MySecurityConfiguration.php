<?php

namespace App\PHP;

use Core\EnableSecurity;
use Core\HttpSecurity;
use Core\RequestMethod;
use Core\SecurityConfiguration;

#[EnableSecurity]
class MySecurityConfiguration extends SecurityConfiguration
{
    function httpConfigure(HttpSecurity $http){
        $http->authorizeRequest()
            ->antMatchers(RequestMethod::GET, '/member-list')
            ->hasRole("ADMIN")
            ->antMatchers(RequestMethod::GET, '/product-add')
            ->hasRole("ADMIN")
            ->antMatchers(RequestMethod::GET, '/member-info/$mid')
            ->hasRole("ADMIN")
            ->antMatchers(RequestMethod::POST, '/delete-member')
            ->hasRole("ADMIN")
            ->antMatchers(RequestMethod::POST, '/edit-product')
            ->hasRole("ADMIN")
            ->antMatchers(RequestMethod::POST, '/delete-product')
            ->hasRole("ADMIN")
            ->antMatchers(RequestMethod::POST, '/save-boardgame')
            ->hasRole("ADMIN")
            ->antMatchers(RequestMethod::POST, '/add-comment')
            ->hasRole("ADMIN")->hasRole("MEMBER")
            ->antMatchers(RequestMethod::POST, '/edit-comment')
            ->hasRole("ADMIN")
            ->antMatchers(RequestMethod::POST, '/delete-comment')
            ->hasRole("ADMIN")
            ->antMatchers(RequestMethod::GET, '/favourite')
            ->hasRole("BANISHED")->hasRole("MEMBER")->hasRole("ADMIN")
            ->antMatchers(RequestMethod::GET, '/favourite/$bid')
            ->hasRole("BANISHED")->hasRole("MEMBER")->hasRole("ADMIN")
            ->antMatchers(RequestMethod::POST, '/add-favourite')
            ->hasRole("BANISHED")->hasRole("MEMBER")->hasRole("ADMIN")
            ->antMatchers(RequestMethod::POST, '/delete-favourite')
            ->hasRole("BANISHED")->hasRole("MEMBER")->hasRole("ADMIN")
            ->antMatchers(RequestMethod::GET, '/member')
            ->hasRole("BANISHED")->hasRole("MEMBER")->hasRole("ADMIN")
            ->antMatchers(RequestMethod::GET, '/members')
            ->hasRole("ADMIN")
            ->antMatchers(RequestMethod::GET, '/member/boardgames')
            ->hasRole("BANISHED")->hasRole("MEMBER")->hasRole('ADMIN')
            ->antMatchers(RequestMethod::GET, '/member/img')
            ->hasRole("BANISHED")->hasRole("MEMBER")->hasRole("ADMIN")
            ->antMatchers(RequestMethod::GET, '/member-info')
            ->hasRole("ADMIN")->hasRole("MEMBER")->hasRole("BANISHED")
            ->antMatchers(RequestMethod::POST, '/save-user')
            ->hasRole("ADMIN")->hasRole("MEMBER")->hasRole("BANISHED")
            ->antMatchers(RequestMethod::POST, '/save-info')
            ->hasRole("ADMIN")->hasRole("MEMBER")->hasRole("BANISHED")
        ;
    }
}