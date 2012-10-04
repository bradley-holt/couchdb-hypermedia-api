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

  package { "epel-release":
    provider => "rpm",
    ensure => "installed",
    source => "http://dl.iuscommunity.org/pub/ius/stable/Redhat/6/x86_64/epel-release-6-5.noarch.rpm"
  }

  package { "ius-release":
    require => Package["epel-release"],
    provider => "rpm",
    ensure => "installed",
    source => "http://dl.iuscommunity.org/pub/ius/stable/Redhat/6/x86_64/ius-release-1.0-10.ius.el6.noarch.rpm"
  }

  package { "php54":
    require => [
      Package["ius-release"],
      Package["httpd"],
    ],
    ensure => "latest",
    notify => Service["httpd"],
  }

  file { "/etc/php.ini":
    require => Package["php54"],
    ensure => "file",
    owner => "root",
    group => "root",
    mode => "0644",
    source => "/vagrant/manifests/etc/php.ini",
    notify => Service["httpd"],
  }

  package { "php54-devel":
    require => Package["php54"],
    ensure => "latest",
    notify => Service["httpd"],
  }
 
  package { "php54-intl":
    require => Package["php54"],
    ensure => "latest",
    notify => Service["httpd"],
  }

  package { "php54-pecl-apc":
    require => Package["php54"],
    ensure => "latest",
    notify => Service["httpd"],
  }

  package { "php54-pecl-xdebug":
    require => Package["php54"],
    ensure => "latest",
    notify => Service["httpd"],
  }

  package { "php54-xml":
    require => Package["php54"],
    ensure => "latest",
    notify => Service["httpd"],
  }

  package { "curl":
    ensure => "latest",
  }

  package { "git":
    ensure => "latest",
  }

}

include minimal-centos-60
