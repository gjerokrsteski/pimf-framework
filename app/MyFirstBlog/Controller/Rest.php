<?php
class MyFirstBlog_Controller_Rest extends Pimf_Controller_Rest implements Pimf_Contracts_RestFull
{
  /**
   * Used to create a new object on the server.
   * Used to modify an existing object on the server.
   * Used to remove an object on the server.
   * @return mixed
   */
  public function postAction()
  {
    echo new Pimf_View_Json($this->data);
  }

  /**
   * Used for basic read requests to the server.
   * @return mixed
   */
  public function getAction()
  {
    echo new Pimf_View_Json($this->data);
  }
}
