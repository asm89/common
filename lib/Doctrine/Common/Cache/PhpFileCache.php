<?php

/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license. For more information, see
 * <http://www.doctrine-project.org>.
 */

namespace Doctrine\Common\Cache;

/**
 * Filesystem cache provider.
 *
 * In order to use the PhpFileCache the values saved should work with
 * var_export. This mean that objects should implement __set_state().
 *
 * @since  2.3
 * @author Alexander Mols <iam.asm89@gmail.com>
 */
class PhpFileCache extends CacheProvider
{
    /**
     * Path to save the cache files.
     *
     * @var string
     */
    private $path;

    /**
     * @var integer
     */
    private $permissions = 0755;

    /**
     * Get the save path.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set the save path
     *
     * @param string $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * Get the file permissions
     *
     * @return integer
     */
    public function getPermissions()
    {
        return $this->permissions;
    }

    /**
     * Set the file permissions
     *
     * @param integer $permissions
     */
    public function setPermissions($permissions)
    {
        $this->permissions = $permissions;
    }

    /**
     * {@inheritdoc}
     */
    protected function doFetch($id)
    {
        $path = $this->getFilePath($id);

        if (!file_exists($path)) {
            return false;
        }

        return include $path;
    }

    /**
     * {@inheritdoc}
     */
    protected function doContains($id)
    {
        return file_exists($this->getFilePath($id));
    }

    /**
     * {@inheritdoc}
     */
    protected function doSave($id, $data, $lifeTime = 0)
    {
        $path = $this->getFilePath($id);

        file_put_contents($path, '<?php return '.var_export($data, true).';');
        chmod($path, $this->permissions);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    protected function doDelete($id)
    {
        unlink($this->getFilePath($id));

        return true;
    }

    /**
     * {@inheritdoc}
     */
    protected function doFlush()
    {
        $files = glob($this->path.'/*.php');

        foreach ($files as $file) {
            unlink($file);
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    protected function doGetStats()
    {
        return null;
    }

    /**
     * Get file path
     *
     * @param string $id
     *
     * @return string
     */
    private function getFilePath($id)
    {
        return $this->path.'/'.sha1($id).'.php';
    }
}
