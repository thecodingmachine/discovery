<?php
declare(strict_types=1);

namespace TheCodingMachine\Discovery\Utils;

/**
 * FileSystem utility class.
 *
 * Some piece of code shameless copied from Symfony FileSystem. Thanks folks!
 * Note: we don't include Symfony FileSystem class in order not to depend on a particular version.
 */
class FileSystem
{
    /**
     * Creates a directory recursively.
     *
     * @param string $dir The directory path
     * @param int    $mode The directory mode
     *
     * @throws IOException On any directory creation failure
     */
    public function mkdir(string $dir, $mode = 0777)
    {
        if (is_dir($dir)) {
            return;
        }

        if (true !== @mkdir($dir, $mode, true)) {
            $error = error_get_last();
            if (!is_dir($dir)) {
                // The directory was not created by a concurrent process. Let's throw an exception with a developer friendly error message if we have one
                if ($error) {
                    throw new IOException(sprintf('Failed to create "%s": %s.', $dir, $error['message']), 0, null, $dir);
                }
                throw new IOException(sprintf('Failed to create "%s"', $dir), 0, null, $dir);
            }
        }
    }

    /**
     * Atomically dumps content into a file.
     *
     * @param string $filename The file to be written to
     * @param string $content  The data to write into the file
     *
     * @throws IOException If the file cannot be written to.
     */
    public function dumpFile(string $filename, string $content)
    {
        $dir = dirname($filename);

        if (!is_dir($dir)) {
            $this->mkdir($dir);
        } elseif (!is_writable($dir)) {
            throw new IOException(sprintf('Unable to write to the "%s" directory.', $dir), 0, null, $dir);
        }

        // Will create a temp file with 0600 access rights
        // when the filesystem supports chmod.
        $tmpFile = $this->tempnam($dir, basename($filename));

        if (false === @file_put_contents($tmpFile, $content)) {
            throw new IOException(sprintf('Failed to write file "%s".', $filename), 0, null, $filename);
        }

        @chmod($tmpFile, 0666 & ~umask());
        $this->rename($tmpFile, $filename, true);
    }

    /**
     * Renames a file or a directory.
     *
     * @param string $origin    The origin filename or directory
     * @param string $target    The new filename or directory
     * @param bool   $overwrite Whether to overwrite the target if it already exists
     *
     * @throws IOException When target file or directory already exists
     * @throws IOException When origin cannot be renamed
     */
    public function rename(string $origin, string $target, bool $overwrite = false)
    {
        // we check that target does not exist
        if (!$overwrite && $this->isReadable($target)) {
            throw new IOException(sprintf('Cannot rename because the target "%s" already exists.', $target), 0, null, $target);
        }

        if (true !== @rename($origin, $target)) {
            throw new IOException(sprintf('Cannot rename "%s" to "%s".', $origin, $target), 0, null, $target);
        }
    }

    /**
     * Tells whether a file exists and is readable.
     *
     * @param string $filename Path to the file
     *
     * @return bool
     *
     * @throws IOException When windows path is longer than 258 characters
     */
    private function isReadable(string $filename)
    {
        if ('\\' === DIRECTORY_SEPARATOR && strlen($filename) > 258) {
            throw new IOException('Could not check if file is readable because path length exceeds 258 characters.', 0, null, $filename);
        }

        return is_readable($filename);
    }

    /**
     * Creates a temporary file with support for custom stream wrappers.
     *
     * @param string $dir    The directory where the temporary filename will be created
     * @param string $prefix The prefix of the generated temporary filename
     *                       Note: Windows uses only the first three characters of prefix
     *
     * @return string The new temporary filename (with path), or throw an exception on failure
     */
    public function tempnam(string $dir, string $prefix) : string
    {
        list($scheme, $hierarchy) = $this->getSchemeAndHierarchy($dir);
        // If no scheme or scheme is "file" or "gs" (Google Cloud) create temp file in local filesystem
        if (null === $scheme || 'file' === $scheme || 'gs' === $scheme) {
            $tmpFile = @tempnam($hierarchy, $prefix);
            // If tempnam failed or no scheme return the filename otherwise prepend the scheme
            if (false !== $tmpFile) {
                if (null !== $scheme && 'gs' !== $scheme) {
                    return $scheme.'://'.$tmpFile;
                }
                return $tmpFile;
            }
            throw new IOException('A temporary file could not be created.');
        }
        // Loop until we create a valid temp file or have reached 10 attempts
        for ($i = 0; $i < 10; ++$i) {
            // Create a unique filename
            $tmpFile = $dir.'/'.$prefix.uniqid(mt_rand(), true);
            // Use fopen instead of file_exists as some streams do not support stat
            // Use mode 'x+' to atomically check existence and create to avoid a TOCTOU vulnerability
            $handle = @fopen($tmpFile, 'x+');
            // If unsuccessful restart the loop
            if (false === $handle) {
                continue;
            }
            // Close the file if it was successfully opened
            @fclose($handle);
            return $tmpFile;
        }
        throw new IOException('A temporary file could not be created.');
    }

    /**
     * Gets a 2-tuple of scheme (may be null) and hierarchical part of a filename (e.g. file:///tmp -> array(file, tmp)).
     *
     * @param string $filename The filename to be parsed
     *
     * @return array The filename scheme and hierarchical part
     */
    private function getSchemeAndHierarchy(string $filename) : array
    {
        $components = explode('://', $filename, 2);
        return 2 === count($components) ? array($components[0], $components[1]) : array(null, $components[0]);
    }
}