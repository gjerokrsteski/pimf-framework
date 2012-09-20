<?php
class MyFirstBlog_Controller_Index extends Pimf_Controller_Abstract
{
  /**
   * @param Pimf_View $view
   * @return string
   */
  protected function loadMainView(Pimf_View $view)
  {
    $viewMain = new Pimf_View();
    $viewMain->setTemplate('theblog');
    $viewMain->assign('blog_title', 'This is my firs Blog with PIMF');
    $viewMain->assign('blog_content', $view->loadTemplate());
    $viewMain->assign('blog_footer', 'A Blog about cool and thin framework');

    return $viewMain->loadTemplate();
  }

  public function indexAction()
  {
    $viewAllEntries = new Pimf_View();

    $registry = new Pimf_Registry();
    $entries  = $registry->em->entry->getAll();

    $viewAllEntries->setTemplate('default');
    $viewAllEntries->assign('entries', $entries);

    echo $this->loadMainView($viewAllEntries);
  }

  public function showentryAction()
  {
    $validator = new Pimf_Util_Validator($this->request->fromGet());

    if (!$validator->digit('id') || !$validator->value('id', '>', 0)) {
      throw new Pimf_Controller_Exception('not valid entry');
    }

    $viewSingleEntry = new Pimf_View();
    $viewSingleEntry->setTemplate('entry');

    $registry = new Pimf_Registry();
    $entry    = $registry->em->entry->find(
      $this->request->fromGet()->getParam('id')
    );

    $viewSingleEntry->assign('title', $entry->getTitle());
    $viewSingleEntry->assign('content', $entry->getContent());
    $viewSingleEntry->assign('back_link_title', 'Back to overview');

    echo $this->loadMainView($viewSingleEntry);
  }

  public function insertCliAction()
  {
    $registry = new Pimf_Registry();
    $entry    = new MyFirstBlog_Models_Entry();

    $entry->setTitle($this->request->fromGet()->getParam('title'));
    $entry->setContent($this->request->fromGet()->getParam('content'));

    $res = $registry->em->entry->insert($entry);

    var_dump($res);
  }

  public function updateCliAction()
  {
    $registry = new Pimf_Registry();
    $entry    = new MyFirstBlog_Models_Entry();

    $entry->setTitle($this->request->fromGet()->getParam('title'));
    $entry->setContent($this->request->fromGet()->getParam('content'));

    $entry = $registry->em->entry->reflectId(
      $entry, $this->request->fromGet()->getParam('id')
    );

    $res = $registry->em->entry->update($entry);

    var_dump($res);
  }
}