<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


if ( ! function_exists('css_url'))
{

    function css_url($nom)
    {
        return base_url() . 'assets/css/' . $nom . '.css';
    }

}


if ( ! function_exists('js_url'))
{

    function js_url($nom)
    {
        return base_url() . 'assets/js/' . $nom . '.js';
    }

}


if ( ! function_exists('img_url'))
{

    function img_url($nom, $dossier)
    {
        if (empty($dossier)){
            return base_url() . 'assets/img/' . $nom;
        }else{
            return base_url() . 'assets/img/' . $dossier . '/' . $nom;
        }
    }

}


if ( ! function_exists('img'))
{

    function img($nom, $alt = '')

    {
        return '<img src="' . img_url($nom) . '" alt="' . $alt . '" />';
    }

}