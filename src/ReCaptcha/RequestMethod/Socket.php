<?php

declare(strict_types=1);

/**
 * This is a PHP library that handles calling reCAPTCHA.
 *
 * BSD 3-Clause License
 *
 * @copyright (c) 2019, Google Inc.
 *
 * @see https://www.google.com/recaptcha
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 * 1. Redistributions of source code must retain the above copyright notice, this
 *    list of conditions and the following disclaimer.
 *
 * 2. Redistributions in binary form must reproduce the above copyright notice,
 *    this list of conditions and the following disclaimer in the documentation
 *    and/or other materials provided with the distribution.
 *
 * 3. Neither the name of the copyright holder nor the names of its
 *    contributors may be used to endorse or promote products derived from
 *    this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE
 * FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL
 * DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
 * SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY,
 * OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

namespace ReCaptcha\RequestMethod;

/**
 * Convenience wrapper around native socket and stream functions to allow mocking.
 */
class Socket
{
    /**
     * @var mixed
     */
    private $handle;

    /**
     * @param string     $hostname
     * @param int        $port
     * @param int        $errno
     * @param string     $errstr
     * @param null|float $timeout
     *
     * @return mixed
     */
    public function fsockopen($hostname, $port = -1, &$errno = 0, &$errstr = '', $timeout = null)
    {
        $timeout = is_null($timeout) ? floatval(ini_get('default_socket_timeout')) : $timeout;
        $this->handle = fsockopen($hostname, $port, $errno, $errstr, $timeout);

        if (false !== $this->handle && 0 === $errno && '' === $errstr) {
            return $this->handle;
        }

        if (false !== $this->handle) {
            $this->fclose();
        }

        return false;
    }

    public function streamSetTimeout(int $seconds): bool
    {
        // @phpstan-ignore argument.type
        return stream_set_timeout($this->handle, $seconds);
    }

    public function fwrite(string $string): false|int
    {
        // @phpstan-ignore argument.type
        return fwrite($this->handle, $string);
    }

    public function streamGetContents(): false|string
    {
        // @phpstan-ignore argument.type
        return stream_get_contents($this->handle);
    }

    public function fclose(): bool
    {
        // @phpstan-ignore argument.type
        return fclose($this->handle);
    }
}
