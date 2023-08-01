<?php

declare(strict_types=1);

namespace App\Support\Http\Responses;

class StreamResponse extends Response
{
    /**
     * @noinspection MkdirRaceConditionInspection
     */
    public function save(string $directory, string $filename = ''): string
    {
        $this->getBody()->rewind();

        $directory = rtrim($directory, '/');

        if (! is_dir($directory)) {
            mkdir($directory, 0o755, true); // @codeCoverageIgnore
        }

        if (! is_writable($directory)) {
            throw new \InvalidArgumentException(sprintf("'%s' is not writable.", $directory));
        }

        $contents = $this->getBody()->getContents();

        if (empty($filename)) {
            if (preg_match('/filename="(?<filename>.*?)"/', $this->getHeaderLine('Content-Disposition'), $match)) {
                $filename = $match['filename'];
            } else {
                $filename = md5($contents);
            }
        }

        if (empty(pathinfo($filename, PATHINFO_EXTENSION))) {
            // $filename .= File::getStreamExt($contents);
            $filename .= $this->getStreamExt();
        }

        file_put_contents($directory.'/'.$filename, $contents);

        return $filename;
    }

    /**
     * @throws \Overtrue\Http\Exceptions\InvalidArgumentException
     */
    public function saveAs(string $directory, string $filename): string
    {
        return $this->save($directory, $filename);
    }

    private function getStreamExt(): ?string
    {
        $wrapperData = (array) $this->getBody()->getMetadata('wrapper_data');

        if (preg_match('/\bstream\/(.+)$/', $wrapperData[0] ?? '', $matches)) {
            return $matches[1];
        }

        return null;
    }
}
