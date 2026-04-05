<?php
enum HeaderName: string {
  case Accept = "HTTP_ACCEPT";
  case RequestId = "HTTP_X_ORTHANC_REQUEST_ID";
  case Device = "HTTP_X_ORTHANC_DEVICE";
  case OrthancPlafromVersion = "X-ORTHANC-PLATFORM-VERSION";
}
