<?php
namespace Fixture\Controller;

class Rest extends \Pimf\Controller\Rest
{
  public function postAction()
  {
    return $this->data;
  }

  public function getAction()
  {
    return $this->data;
  }
}
