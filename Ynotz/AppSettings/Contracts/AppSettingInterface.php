<?php
namespace Modules\Ynotz\AppSettings\Contracts;

interface AppSettingInterface
{
    public function store(array $data): void;
    public function update(array $data): void;
}
?>
