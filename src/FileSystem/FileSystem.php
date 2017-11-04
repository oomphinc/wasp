<?php

namespace OomphInc\WASP\FileSystem;

use RuntimeException;

class FileSystem extends AbstractFileSystem {

	/**
	 * @param string [$cwd] optional starting current working directory
	 */
	public function __construct($cwd = null) {
		$this->setCwd($cwd);
	}

	/**
	 * @inheritDoc
	 */
	public function setCwd($path) {
		parent::setCwd($path === null ? getcwd() : $path);
	}

	/**
	 * @inheritDoc
	 */
	public function resolvePath($path) {
		// is the path relative?
		if (!preg_match('#^/|[a-z]+://#', $path)) {
			$path = $this->getCwd() . '/' . $path;
		}
		return is_readable($path) ? realpath($path) : $path;
	}

	/**
	 * @inheritDoc
	 */
	public function getFiles($pattern, $relative = true) {
		$cwd = $this->getCwd();
		$files = [];
		foreach (glob($cwd . '/' . $pattern, GLOB_BRACE) as $file) {
			// strip off the files root path
			$files[] = $relative ? preg_replace('#^' . preg_quote(rtrim($cwd, '/') . '/', '#') . '#', '', $file) : $file;
		}
		return $files;
	}

	/**
	 * @inheritDoc
	 */
	public function fileExists($path) {
		return is_readable($this->resolvePath($path));
	}

	/**
	 * @inheritDoc
	 */
	public function readFile($path) {
		$file = file_get_contents($this->resolvePath($path));
		if ($file === false) {
			throw new RuntimeException("Could not read file '$path'");
		}
		return $file;
	}

	/**
	 * @inheritDoc
	 */
	public function writeFile($path, $contents) {
		if (file_put_contents($this->resolvePath($path), $contents) === false) {
			throw new RuntimeException("Could not write to file '$path'");
		}
	}

	/**
	 * @inheritDoc
	 */
	public function deleteFile($path) {
		if ($this->fileExists($path)) {
			if (!unlink($this->resolvePath($path))) {
				throw new RuntimeException("Could not delete file '$path'");
			}
		}
	}

	/**
	 * @inheritDoc
	 */
	public function renameFile($oldPath, $newPath) {
		if ($this->fileExists($oldPath)) {
			if (!rename($this->resolvePath($oldPath), $this->resolvePath($newPath))) {
				throw new RuntimeException("Could not rename file '$oldPath'");
			}
		}
	}

	/**
	 * @inheritDoc
	 */
	public function mkDir($path) {
		if (!mkdir($this->resolvePath($path), 0777, true)) {
			throw new RuntimeException("Could not create directory '$path'");
		}
	}

	/**
	 * @inheritDoc
	 */
	public function rmDir($path) {
		exec('rm -rf ' . escapeshellarg($this->resolvePath($path)), $output, $exitCode);
		if ($exitCode !== 0) {
			throw new RuntimeException("Could not delete directory '$path'");
		}
	}

}
