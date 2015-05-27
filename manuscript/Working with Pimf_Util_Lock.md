 * A locked file is created based on the lock name when calling lock(). Other
 * lock handlers will not be able to lock the same name until it is released
 * (explicitly by calling release() or implicitly when the instance holding the
 * lock is destroyed).