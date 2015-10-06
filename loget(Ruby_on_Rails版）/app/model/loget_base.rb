class LogetBase < ActiveRecord::Base
  establish_connection(:openfire)
  # attr_accessible :title, :body
end
