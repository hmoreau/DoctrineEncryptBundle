<?php

/*
 * This file is part of the DoctrineEncryptBundle package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ambta\DoctrineEncryptBundle\Encryptors;

use ParagonIE\Halite\HiddenString;
use ParagonIE\Halite\KeyFactory;

/**
 * Class for encrypting and decrypting with the halite library.
 *
 * @author Michael de Groot <specamps@gmail.com>
 */
class HaliteEncryptor implements EncryptorInterface
{
    private $encryptionKey;
    private $keyFile;

    /**
     * {@inheritdoc}
     */
    public function __construct(string $keyFile)
    {
        $this->keyFile = $keyFile;
    }

    /**
     * {@inheritdoc}
     */
    public function encrypt($data)
    {
        return \ParagonIE\Halite\Symmetric\Crypto::encrypt(new HiddenString($data), $this->getKey());
    }

    /**
     * {@inheritdoc}
     */
    public function decrypt($data)
    {
        $data = \ParagonIE\Halite\Symmetric\Crypto::decrypt($data, $this->getKey());

        if ($data instanceof HiddenString) {
            $data = $data->getString();
        }

        return $data;
    }

    private function getKey()
    {
        if (null === $this->encryptionKey) {
            try {
                $this->encryptionKey = \ParagonIE\Halite\KeyFactory::loadEncryptionKey($this->keyFile);
            } catch (\ParagonIE\Halite\Alerts\CannotPerformOperation $e) {
                $this->encryptionKey = KeyFactory::generateEncryptionKey();
                \ParagonIE\Halite\KeyFactory::save($this->encryptionKey, $this->keyFile);
            }
        }

        return $this->encryptionKey;
    }
}
