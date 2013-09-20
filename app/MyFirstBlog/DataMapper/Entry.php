<?php
class MyFirstBlog_DataMapper_Entry extends Pimf_DataMapper_Abstract
{
  /**
   * @return MyFirstBlog_Model_Entry[]
   */
  public function getAll()
  {
    $sth = $this->db->prepare(
      'SELECT * FROM blog'
    );

    $sth->setFetchMode(
      PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE,
      'MyFirstBlog_Model_Entry',
      array('title', 'content')
    );

    $sth->execute();

    return $sth->fetchAll();
  }

  /**
   * @param int $id
   * @return MyFirstBlog_Model_Entry
   * @throws OutOfRangeException
   */
  public function find($id)
  {
    if (true === $this->identityMap->hasId($id)) {
      return $this->identityMap->getObject($id);
    }

    $sth = $this->db->prepare(
      'SELECT * FROM blog WHERE id = :id'
    );

    $sth->bindValue(':id', $id, PDO::PARAM_INT);

    $sth->setFetchMode(
      PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE,
      'MyFirstBlog_Model_Entry',
      array('title', 'content')
    );

    $sth->execute();

    // let pdo fetch the User instance for you.
    $blogEntry = $sth->fetch();

    if ($blogEntry === false) {
      throw new OutOfRangeException('no entry with id='.$id);
    }

    // set the protected id of user via reflection.
    $blogEntry = $this->reflect($blogEntry, $id);

    $this->identityMap->set($id, $blogEntry);

    return $blogEntry;
  }

  /**
   * @param MyFirstBlog_Model_Entry $blogEntry
   * @return int
   * @throws RuntimeException
   */
  public function insert(MyFirstBlog_Model_Entry $blogEntry)
  {
    if (true === $this->identityMap->hasObject($blogEntry)) {
      throw new RuntimeException('Object has an ID, cannot insert.');
    }

    $sth = $this->db->prepare(
      "INSERT INTO blog (title, content) VALUES (:title, :content)"
    );

    $sth->bindValue(':title', $blogEntry->getTitle());
    $sth->bindValue(':content', $blogEntry->getContent());
    $sth->execute();

    $id = (int)$this->db->lastInsertId();

    $blogEntry = $this->reflect($blogEntry, $id);

    $this->identityMap->set($id, $blogEntry);

    return $id;
  }

  /**
   * @param MyFirstBlog_Model_Entry $blogEntry
   * @return bool
   */
  public function update(MyFirstBlog_Model_Entry $blogEntry)
  {
    $sth = $this->db->prepare(
      "UPDATE blog SET title = :title, content = :content WHERE id = :id"
    );

    $sth->bindValue(':title', $blogEntry->getTitle());
    $sth->bindValue(':content', $blogEntry->getContent());
    $sth->bindValue(':id', $blogEntry->getId(), PDO::PARAM_INT);

    $sth->execute();

    if ($sth->rowCount() == 1) {
      return true;
    }

    return false;
  }

  /**
   * @param int $id
   * @return bool
   */
  public function delete($id)
  {
    $sth = $this->db->prepare(
      "DELETE FROM blog WHERE id = :id"
    );

    $sth->bindValue(':id', $id, PDO::PARAM_INT);
    $sth->execute();

    if ($sth->rowCount() == 0) {
      return false;
    }

    return true;
  }
}
