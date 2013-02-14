<?php
/**
 * Pimf
 *
 * PHP Version 5
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.
 * It is also available through the world-wide-web at this URL:
 * http://krsteski.de/new-bsd-license/
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to gjero@krsteski.de so we can send you a copy immediately.
 *
 * @copyright Copyright (c) 2010-2011 Gjero Krsteski (http://krsteski.de)
 * @license http://krsteski.de/new-bsd-license New BSD License
 */

/**
 * @package Pimf_Session_Storages
 * @author Gjero Krsteski <gjero@krsteski.de>
 */
class Pimf_Session_Storages_Pdo
  extends Pimf_Session_Storages_Storage
  implements Pimf_Session_Storages_Cleaner
{
  /**
   * @var Pimf_Pdo
   */
  protected $db;

  /**
   * @param Pimf_Pdo $pdo
   */
  public function __construct(Pimf_Pdo $pdo)
  {
    $this->db = $pdo;
  }

  /**
   * Load a session from storage by a given ID.
   * If no session is found for the ID, null will be returned.
   * @param string $id
   * @return array|null
   */
  public function load($id)
  {
    try {
      $sth = $this->db->prepare(
        'SELECT * FROM sessions WHERE id = :id'
      );

      $sth->bindValue(':id', $id, PDO::PARAM_INT);
      $sth->execute();

      $session = $sth->fetchObject();

      if ($session instanceof stdClass) {
        return array(
          'id'            => $session->id,
          'last_activity' => $session->last_activity,
          'data'          => unserialize($session->data)
        );
      }
    } catch (PDOException $e) {
      return null;
    }
  }

  /**
   * Save a given session to storage.
   * @param array $session
   * @param array $config
   * @param bool $exists
   */
  public function save($session, $config, $exists)
  {
    if ($exists) {
      $sth = $this->db->prepare(
        "INSERT INTO sessions (id, last_activity, data) VALUES (:id, :last_activity, :data)"
      );
    } else {
      $sth = $this->db->prepare(
        "UPDATE sessions SET last_activity = :last_activity, data = :data WHERE id = :id"
      );
    }

    $sth->bindValue(':id', $session['id'], PDO::PARAM_INT);
    $sth->bindValue(':last_activity', $session['last_activity']);
    $sth->bindValue(':data', serialize($session['data']));
    $sth->execute();
  }

  /**
   * Delete a session from storage by a given ID.
   * @param string $id
   */
  public function delete($id)
  {
    $sth = $this->db->prepare(
      "DELETE FROM sessions WHERE id = :id"
    );

    $sth->bindValue(':id', $id, PDO::PARAM_INT);
    $sth->execute();
  }

  /**
   * Delete all expired sessions from persistent storage.
   * @param int $expiration
   * @return mixed|void
   */
  public function clean($expiration)
  {
    $sth = $this->db->prepare(
      "DELETE FROM sessions WHERE last_activity < :expiration"
    );

    $sth->bindValue(':expiration', $expiration);
    $sth->execute();
  }
}