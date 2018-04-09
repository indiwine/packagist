<?php
/**
 * Created by PhpStorm.
 * User: indiwine
 * Date: 4/9/2018
 * Time: 12:55 PM
 */

namespace Packagist\WebBundle\Service;


use Defuse\Crypto\Crypto;
use Defuse\Crypto\Exception\WrongKeyOrModifiedCiphertextException;
use Defuse\Crypto\Key;
use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;

class Encryption
{
    const FILE_NAME = 'enc.key';

    /**
     * @var LoggerInterface
     */
    private $logger;

    private $key;

    public function __construct(LoggerInterface $logger, $dir, Filesystem $filesystem)
    {
        $this->logger = $logger;


        $keyPath = $dir . '/' . self::FILE_NAME;

        $logger->debug('Check Private Key directory');
        if (!$filesystem->exists($dir)) {
            $logger->debug('Create directory to hold private keys');
            $filesystem->mkdir($dir);
        }

        $logger->debug('Checking key');

        if ($filesystem->exists($keyPath)) {
            $logger->debug('Reading encryption key');
            $this->key = Key::loadFromAsciiSafeString(file_get_contents($keyPath));
        } else {
            $logger->debug('Generating a new random key');
            $this->key = Key::createNewRandomKey();
            $filesystem->dumpFile($keyPath, $this->key->saveToAsciiSafeString());
        }

    }

    /**
     * Encrypt string
     * @param string $str
     * @return string
     * @throws \Defuse\Crypto\Exception\EnvironmentIsBrokenException
     */
    public function encrypt($str)
    {
        $str = (string)$str;
        return Crypto::encrypt($str, $this->key);
    }

    /**
     * Decrypt string
     * @param string $str
     * @return string
     * @throws \RuntimeException
     * @throws \Defuse\Crypto\Exception\EnvironmentIsBrokenException
     */
    public function decrypt($str)
    {
        $str = (string)$str;
        try {
            return Crypto::decrypt($str, $this->key);
        } catch (WrongKeyOrModifiedCiphertextException $exception) {
            $this->logger->critical('Cannot decrypt data', [$exception, $str]);
            throw new \RuntimeException('Some Data cannot be decrypted. See logs for more information');
        }
    }
}