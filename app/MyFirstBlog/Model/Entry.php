<?php
/**
 * Class for data access
 */
class MyFirstBlog_Model_Entry extends ArrayObject
{
  protected $id;

  protected $title;

  protected $content;

  public function setContent($content)
  {
    $this->content = $content;
  }

  public function getContent()
  {
    return $this->content;
  }

  public function getId()
  {
    return $this->id;
  }

  public function setTitle($title)
  {
    $this->title = $title;
  }

  public function getTitle()
  {
    return $this->title;
  }
}
