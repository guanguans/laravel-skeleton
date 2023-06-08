<?php

declare(strict_types=1);

namespace App\Support;

/**
 * @see https://github.com/utopia-php/system/blob/main/src/System/System.php
 */
class System
{
    public const X86 = 'x86';

    public const PPC = 'ppc';

    public const ARM = 'arm';

    private const RegExX86 = '/(x86*|i386|i686)/';

    private const RegExARM = '/(aarch*|arm*)/';

    private const RegExPPC = '/(ppc*)/';

    /**
     * A list of Linux Disks that are not considered valid
     * These are usually virtual drives or other non-physical devices such as loopback or ram.
     *
     * This list is ran through a contains, meaning for example if 'loop' was in the list,
     * A 'loop0' interface would be considered invalid and not computed.
     *
     * Documentation:
     * Loop - https://man7.org/linux/man-pages/man4/loop.4.html
     * Ram - https://man7.org/linux/man-pages/man4/ram.4.html
     */
    private const INVALIDDISKS = [
        'loop',
        'ram',
    ];

    /**
     * A list of Linux Network Interfaces that are not considered valid
     * These are usually virtual interfaces created by tools such as Docker or VirtualBox
     *
     * This list is ran through a contains, meaning for example if 'vboxnet' was in the list,
     * A 'vboxnet0' interface would be considered invalid and not computed.
     *
     * Documentation:
     * veth - https://man7.org/linux/man-pages/man4/veth.4.html
     * docker - https://docs.docker.com/network/
     * lo - Localhost Loopback device, https://man7.org/linux/man-pages/man4/loop.4.html
     * tun - Linux Layer 3 Interface, https://www.kernel.org/doc/html/v5.8/networking/tuntap.html
     * vboxnet - Virtual Machine Networking Interface, https://www.virtualbox.org/manual/ch06.html
     * bonding_masters - https://www.kernel.org/doc/Documentation/networking/bonding.txt
     */
    private const INVALIDNETINTERFACES = [
        'veth',
        'docker',
        'lo',
        'tun',
        'vboxnet',
        '.',
        'bonding_masters',
    ];

    /**
     * Returns the system's OS.
     */
    public static function getOS(): string
    {
        return php_uname('s');
    }

    /**
     * Returns the architecture of the system's processor.
     */
    public static function getArch(): string
    {
        return php_uname('m');
    }

    /**
     * Returns the architecture's Enum of the system's processor.
     *
     * @throws \Exception
     */
    public static function getArchEnum(): string
    {
        $arch = self::getArch();

        switch (1) {
            case preg_match(self::RegExX86, $arch):
                return System::X86;

                break;

            case preg_match(self::RegExPPC, $arch):
                return System::PPC;

                break;

            case preg_match(self::RegExARM, $arch):
                return System::ARM;

                break;

            default:
                throw new \Exception("'{$arch}' enum not found.");

                break;
        }
    }

    /**
     * Returns the system's hostname.
     */
    public static function getHostname(): string
    {
        return php_uname('n');
    }

    /**
     * Checks if the system is running on an ARM architecture.
     */
    public static function isArm(): bool
    {
        return (bool) preg_match(self::RegExARM, self::getArch());
    }

    /**
     * Checks if the system is running on an X86 architecture.
     */
    public static function isX86(): bool
    {
        return (bool) preg_match(self::RegExX86, self::getArch());
    }

    /**
     * Checks if the system is running on an PowerPC architecture.
     */
    public static function isPPC(): bool
    {
        return (bool) preg_match(self::RegExPPC, self::getArch());
    }

    /**
     * Checks if the system is the passed architecture.
     * You should pass `System::X86`, `System::PPC`, `System::ARM` or an equivalent string.
     *
     * @throws \Exception
     */
    public static function isArch(string $arch): bool
    {
        switch ($arch) {
            case self::X86:
                return self::isX86();

                break;

            case self::PPC:
                return self::isPPC();

                break;

            case self::ARM:
                return self::isArm();

                break;

            default:
                throw new \Exception("'{$arch}' not found.");

                break;
        }
    }

    /**
     * Gets the system's total amount of CPU cores.
     *
     * @throws \Exception
     */
    public static function getCPUCores(): int
    {
        switch (self::getOS()) {
            case 'Linux':
                $cpuinfo = file_get_contents('/proc/cpuinfo');
                preg_match_all('/^processor/m', $cpuinfo, $matches);

                return \count($matches[0]);

            case 'Darwin':
                return (int) shell_exec('sysctl -n hw.ncpu');

            case 'Windows':
                return (int) shell_exec('wmic cpu get NumberOfCores');

            default:
                throw new \Exception(self::getOS().' not supported.');
        }
    }

    /**
     * Get percentage CPU usage (between 0 and 100)
     * Reference for formula: https://stackoverflow.com/a/23376195/17300412
     *
     * @throws \Exception
     *
     * @noinspection OffsetOperationsInspection*/
    public static function getCPUUsage(int $duration = 1): float
    {
        switch (self::getOS()) {
            case 'Linux':
                $startCpu = self::getProcStatData()['total'];
                sleep($duration);
                $endCpu = self::getProcStatData()['total'];

                $prevIdle = $startCpu['idle'] + $startCpu['iowait'];
                $idle = $endCpu['idle'] + $endCpu['iowait'];

                $prevNonIdle = $startCpu['user'] + $startCpu['nice'] + $startCpu['system'] + $startCpu['irq'] + $startCpu['softirq'] + $startCpu['steal'];
                $nonIdle = $endCpu['user'] + $endCpu['nice'] + $endCpu['system'] + $endCpu['irq'] + $endCpu['softirq'] + $endCpu['steal'];

                $prevTotal = $prevIdle + $prevNonIdle;
                $total = $idle + $nonIdle;

                $totalDiff = $total - $prevTotal;
                $idleDiff = $idle - $prevIdle;

                $percentage = ($totalDiff - $idleDiff) / $totalDiff;

                return $percentage * 100;

            default:
                throw new \Exception(self::getOS().' not supported.');
        }
    }

    /**
     * Returns the total amount of RAM available on the system as Megabytes.
     *
     * @throws \Exception
     */
    public static function getMemoryTotal(): int
    {
        switch (self::getOS()) {
            case 'Linux':
                $meminfo = file_get_contents('/proc/meminfo');
                preg_match('/MemTotal:\s+(\d+)/', $meminfo, $matches);

                if (isset($matches[1])) {
                    return \intval(\intval($matches[1]) / 1024);
                }

                throw new \Exception('Could not find MemTotal in /proc/meminfo.');

                break;

            case 'Darwin':
                return (int) ((int) shell_exec('sysctl -n hw.memsize') / 1024 / 1024);

                break;

            default:
                throw new \Exception(self::getOS().' not supported.');
        }
    }

    /**
     * Returns the total amount of Free RAM available on the system as Megabytes.
     *
     * @throws \Exception
     */
    public static function getMemoryFree(): int
    {
        switch (self::getOS()) {
            case 'Linux':
                $meminfo = file_get_contents('/proc/meminfo');
                preg_match('/MemFree:\s+(\d+)/', $meminfo, $matches);
                if (isset($matches[1])) {
                    return (int) ((int) $matches[1] / 1024);
                }

                throw new \Exception('Could not find MemFree in /proc/meminfo.');

            case 'Darwin':
                return \intval(\intval(shell_exec('sysctl -n vm.page_free_count')) / 1024 / 1024);

            default:
                throw new \Exception(self::getOS().' not supported.');
        }
    }

    /**
     * Returns the total amount of Disk space on the system as Megabytes.
     *
     * @throws \Exception
     */
    public static function getDiskTotal(): int
    {
        $totalSpace = disk_total_space(__DIR__);

        if (false === $totalSpace) {
            throw new \Exception('Unable to get disk space');
        }

        return (int) ($totalSpace / 1024 / 1024);
    }

    /**
     * Returns the total amount of Disk space free on the system as Megabytes.
     *
     * @throws \Exception
     */
    public static function getDiskFree(): int
    {
        $totalSpace = disk_free_space(__DIR__);

        if (false === $totalSpace) {
            throw new \Exception('Unable to get free disk space');
        }

        return (int) ($totalSpace / 1024 / 1024);
    }

    /**
     * Returns an array of all the available storage devices on the system containing
     * the current read and write usage in Megabytes.
     * There is also a ['total'] key that contains the total amount of read and write usage.
     *
     * @throws \Exception
     *
     * @noinspection OffsetOperationsInspection*/
    public static function getIOUsage(int $duration = 1): array
    {
        $diskStat = self::getDiskStats();
        sleep($duration);
        $diskStat2 = self::getDiskStats();

        // Remove invalid disks
        $diskStat = array_filter($diskStat, function ($disk) {
            foreach (self::INVALIDDISKS as $filter) {
                if (! isset($disk[2])) {
                    return false;
                }
                if (str_contains($disk[2], $filter)) {
                    return false;
                }
            }

            return true;
        });

        $diskStat2 = array_filter($diskStat2, function ($disk) {
            foreach (self::INVALIDDISKS as $filter) {
                if (! isset($disk[2])) {
                    return false;
                }

                if (str_contains($disk[2], $filter)) {
                    return false;
                }
            }

            return true;
        });

        $stats = [];

        // Compute Delta
        foreach ($diskStat as $key => $disk) {
            $stats[$key]['read'] = (((int) $diskStat2[$key][5] - (int) $disk[5]) * 512 / 1048576);
            $stats[$key]['write'] = (((int) $diskStat2[$key][9] - (int) $disk[9]) * 512 / 1048576);
        }

        $stats['total']['read'] = array_sum(array_column($stats, 'read'));
        $stats['total']['write'] = array_sum(array_column($stats, 'write'));

        return $stats;
    }

    /**
     * Returns an array of all the available network interfaces on the system
     * containing the current download and upload usage in Megabytes.
     * There is also a ['total'] key that contains the total amount of download
     * and upload
     *
     * @param int $duration The buffer duration to fetch the data points
     *
     * @throws \Exception
     *
     * @noinspection OffsetOperationsInspection*/
    public static function getNetworkUsage(int $duration = 1): array
    {
        // Create a list of interfaces
        $interfaces = scandir('/sys/class/net', SCANDIR_SORT_NONE);

        // Remove all unwanted interfaces
        $interfaces = array_filter($interfaces, function ($interface) {
            foreach (self::INVALIDNETINTERFACES as $filter) {
                if (str_contains($interface, $filter)) {
                    return false;
                }
            }

            return true;
        });

        // Get the total IO Usage
        $IOUsage = [];

        foreach ($interfaces as $interface) {
            $tx1 = (int) file_get_contents('/sys/class/net/'.$interface.'/statistics/tx_bytes');
            $rx1 = (int) file_get_contents('/sys/class/net/'.$interface.'/statistics/rx_bytes');
            sleep($duration);
            $tx2 = (int) file_get_contents('/sys/class/net/'.$interface.'/statistics/tx_bytes');
            $rx2 = (int) file_get_contents('/sys/class/net/'.$interface.'/statistics/rx_bytes');

            $IOUsage[$interface]['download'] = round(($rx2 - $rx1) / 1048576, 2);
            $IOUsage[$interface]['upload'] = round(($tx2 - $tx1) / 1048576, 2);
        }

        $IOUsage['total']['download'] = array_sum(array_column($IOUsage, 'download'));
        $IOUsage['total']['upload'] = array_sum(array_column($IOUsage, 'upload'));

        return $IOUsage;
    }

    /**
     * Helper function to read a Linux System's /proc/stat data and convert it into an array.
     *
     * @noinspection OffsetOperationsInspection
     */
    private static function getProcStatData(): array
    {
        $data = [];

        $totalCPUExists = false;

        $cpustats = file_get_contents('/proc/stat');

        $cpus = explode("\n", $cpustats);

        // Remove non-CPU lines
        $cpus = array_filter($cpus, fn ($cpu) => preg_match('/^cpu[0-999]/', $cpu));

        foreach ($cpus as $cpu) {
            $cpu = explode(' ', $cpu);

            // get CPU number
            $cpuNumber = substr($cpu[0], 3);

            if ('cpu' === $cpu[0]) {
                $totalCPUExists = true;
                $cpuNumber = 'total';
            }

            $data[$cpuNumber]['user'] = $cpu[1] ?? 0;
            $data[$cpuNumber]['nice'] = $cpu[2] ?? 0;
            $data[$cpuNumber]['system'] = $cpu[3] ?? 0;
            $data[$cpuNumber]['idle'] = $cpu[4] ?? 0;
            $data[$cpuNumber]['iowait'] = $cpu[5] ?? 0;
            $data[$cpuNumber]['irq'] = $cpu[6] ?? 0;
            $data[$cpuNumber]['softirq'] = $cpu[7] ?? 0;

            // These might not exist on older kernels.
            $data[$cpuNumber]['steal'] = $cpu[8] ?? 0;
            $data[$cpuNumber]['guest'] = $cpu[9] ?? 0;
        }

        if (! $totalCPUExists) {
            // Combine all values
            $data['total'] = [
                'user' => 0,
                'nice' => 0,
                'system' => 0,
                'idle' => 0,
                'iowait' => 0,
                'irq' => 0,
                'softirq' => 0,
                'steal' => 0,
                'guest' => 0,
            ];

            foreach ($data as $cpu) {
                $data['total']['user'] += (int) $cpu['user'];
                $data['total']['nice'] += (int) ($cpu['nice'] ?? 0);
                $data['total']['system'] += (int) ($cpu['system'] ?? 0);
                $data['total']['idle'] += (int) ($cpu['idle'] ?? 0);
                $data['total']['iowait'] += (int) ($cpu['iowait'] ?? 0);
                $data['total']['irq'] += (int) ($cpu['irq'] ?? 0);
                $data['total']['softirq'] += (int) ($cpu['softirq'] ?? 0);
                $data['total']['steal'] += (int) ($cpu['steal'] ?? 0);
                $data['total']['guest'] += (int) ($cpu['guest'] ?? 0);
            }
        }

        return $data;
    }

    /**
     * Helper function to read a Linux System's /proc/diskstats data and convert it into an array.
     *
     * @noinspection OffsetOperationsInspection
     */
    private static function getDiskStats(): array
    {
        // Read /proc/diskstats
        $diskstats = file_get_contents('/proc/diskstats');

        // Split the data
        $diskstats = explode("\n", $diskstats);

        // Remove excess spaces
        $diskstats = array_map(fn ($data) => preg_replace('/\t+/', ' ', trim($data)), $diskstats);

        // Remove empty lines
        $diskstats = array_filter($diskstats, fn ($data) => ! empty($data));

        $data = [];
        foreach ($diskstats as $disk) {
            // Breakdown the data
            $disk = explode(' ', $disk);

            $data[$disk[2]] = $disk;
        }

        return $data;
    }
}
