<?php
enum HeaderName: string {
  case Accept = "HTTP_ACCEPT";
  case RequestId = "HTTP_X_ORTHANC_REQUEST_ID";
  case OrthancPlafromVersion = "X-ORTHANC-PLATFORM-VERSION";
}
