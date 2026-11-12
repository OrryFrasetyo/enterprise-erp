<?php

namespace Modules\CustomerVehicle\Enums;

enum TransmissionType: string
{
    case Manual = 'manual';
    case Matic = 'matic';
    case Cvt = 'cvt';
    case Ev = 'ev';
}
