<?php
namespace Modules\Ynotz\EasyAdmin;

class InputUpdateResponse
{
    public $result;
    public $message;
    public $isvalid;

    public function __construct(mixed $result, string $message, bool $isvalid)
    {
        $this->result = $result;
        $this->message = $message;
        $this->isvalid = $isvalid;
    }
}
?>
