<?php

namespace App;

enum ApplicationStatus: string
{
    case Pending = 'Pending';
    case Accepted = 'Accepted';
    case Rejected = 'Rejected';

}
