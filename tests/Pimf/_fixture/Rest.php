<?php

namespace Fixture\Controller;


class Rest extends \Pimf\Controller\Rest
{
    public function putAction()
    {
        return $this->request->streamInput(true);
    }

    public function deleteAction()
    {
        return $this->request->streamInput(true);
    }

    public function patchAction()
    {
        return $this->request->streamInput(true);
    }
}