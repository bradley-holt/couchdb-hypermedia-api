class minimal-centos-60 {

  group { "puppet":
    ensure => "present",
  }

  file { "/etc/yum.repos.d/cloudant.repo":
    ensure => "file",
    owner => "root",
    group => "root",
    mode => "0644",
    source => "/vagrant/manifests/etc/yum.repos.d/cloudant.repo",
  }

  package { "bigcouch":
    require => File["/etc/yum.repos.d/cloudant.repo"],
    ensure => "latest",
  }

  service { "bigcouch":
    ensure => "running",
    require => Package["bigcouch"]
  }

  package { "httpd":
    ensure => "latest",
  }

  service { "httpd":
    ensure => "running",
    require => [
      Package["httpd"],
    ],
  }

}

include minimal-centos-60
