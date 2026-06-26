<?php

use Illuminate\Support\Facades\Mail;

use App\Mail\NoticeMessage;

Mail::to('pablo_pages@live.com')->send(new NoticeMessage());
