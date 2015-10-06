class People < ActiveLdap::Base
  ldap_mapping dn_attribute: "uid",
               prefix: "ou=People",
               classes: ["posixAccount"]
end
