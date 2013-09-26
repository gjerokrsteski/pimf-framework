<?php
class Fixture_Controller_Rest extends Pimf_Controller_Rest
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
