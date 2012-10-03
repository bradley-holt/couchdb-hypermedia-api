class minimal-centos-60 {

  group { "puppet":
    ensure => "present",
  }

}

include minimal-centos-60
