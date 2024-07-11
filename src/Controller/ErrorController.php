<?php

namespace App\Controller;

class ErrorController extends AbstractController
{

  /**
   * Display home page
   *
   * @return string
   * @throws \Twig\Error\LoaderError
   * @throws \Twig\Error\RuntimeError
   * @throws \Twig\Error\SyntaxError
   */
  public function error404()
  {
    return $this->twig->render('Error/error404.html.twig');
  }
}
