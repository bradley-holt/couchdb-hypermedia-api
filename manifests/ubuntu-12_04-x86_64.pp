class ubuntu-12_04-x86_64 {

  file { "/etc/apt/sources.list.d/couchdb.list":
    ensure => "file",
    owner => "root",
    group => "root",
    mode => "0644",
    source => "/vagrant/manifests/etc/apt/sources.list.d/couchdb.list",
  }

  exec { "apt-key-add-couchdb":
    command => "/usr/bin/apt-key adv --keyserver keyserver.ubuntu.com --recv-keys 56A3D45E",
    require => File["/etc/apt/sources.list.d/couchdb.list"],
  }

  exec { "apt-get update":
    command => "/usr/bin/apt-get update",
    require => Exec["apt-key-add-couchdb"],
  }

  package { "couchdb":
    ensure => "latest",
    require => Exec["apt-get update"],
  }

  service { "couchdb":
    ensure => "running",
    require => Package["couchdb"],
  }

  package { "apache2":
    ensure => "latest",
    require => Exec["apt-get update"],
  }

  file { "/etc/apache2/sites-available/default":
    ensure => "file",
    owner => "root",
    group => "root",
    mode => "0644",
    source => "/vagrant/manifests/etc/apache2/sites-available/default",
    notify => Service["apache2"],
    require => Package["apache2"],
  }

  service { "apache2":
    ensure => "running",
    require => [
      Package["apache2"],
    ],
  }

  package { "php5":
    ensure => "latest",
    notify => Service["apache2"],
    require => Package["apache2"],
  }

  file { "/etc/php5/apache2/php.ini":
    ensure => "file",
    owner => "root",
    group => "root",
    mode => "0644",
    source => "/vagrant/manifests/etc/php5/apache2/php.ini",
    notify => Service["apache2"],
    require => Package["php5"],
  }

  file { "/etc/php5/cli/php.ini":
    ensure => "file",
    owner => "root",
    group => "root",
    mode => "0644",
    source => "/vagrant/manifests/etc/php5/cli/php.ini",
    require => Package["php5"],
  }

  package { "php-apc":
    ensure => "latest",
    notify => Service["apache2"],
    require => Package["php5"],
  }

  package { "php5-curl":
    ensure => "latest",
    notify => Service["apache2"],
    require => Package["php5"],
  }

  package { "php5-dev":
    ensure => "latest",
    notify => Service["apache2"],
    require => Package["php5"],
  }

  package { "php5-intl":
    ensure => "latest",
    notify => Service["apache2"],
    require => Package["php5"],
  }

  package { "php5-xdebug":
    ensure => "latest",
    notify => Service["apache2"],
    require => Package["php5"],
  }

  package { "curl":
    ensure => "latest",
    require => Exec["apt-get update"],
  }

  package { "git":
    ensure => "latest",
    require => Exec["apt-get update"],
  }

  package { "python-pip":
    ensure => "latest",
    require => Exec["apt-get update"],
  }

  exec { "upgrade-httpie":
    command => "/usr/bin/pip install --upgrade httpie",
    require => Package["python-pip"],
  }

  file { "/etc/hosts":
    ensure => "file",
    owner => "root",
    group => "root",
    mode => "0644",
    source => "/vagrant/manifests/etc/hosts",
  }

}

include ubuntu-12_04-x86_64
