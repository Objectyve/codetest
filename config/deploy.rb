set :application, "codetest"
set :repository,  "git@github.com:objectyve/codetest"
set :domain,      "codetest.objectyve.com"
set :deploy_to,   "/var/www/#{application}"
set :user,        "capistrano"
ssh_options[:forward_agent] = true

set :scm, :git
# Or: `accurev`, `bzr`, `cvs`, `darcs`, `git`, `mercurial`, `perforce`, `subversion` or `none`

role :web, "#{domain}"                          # Your HTTP server, Apache/etc
role :app, "#{domain}"                          # This may be the same as your `Web` server
role :db,  "#{domain}", :primary => true        # This is where Rails migrations will run

# if you want to clean up old releases on each deploy uncomment this:
after "deploy:restart", "deploy:cleanup"

namespace :deploy do
  task :finalize_update do
    run "cd #{latest_release} && curl -s http://getcomposer.org/installer | php"
    run "cd #{latest_release} && php composer.phar -v install"
    run "cd #{latest_release} && mkdir -p #{shared_path}/config"
    run "cd #{latest_release} && ln -s #{shared_path}/config/parameters.yml #{latest_release}/config/parameters.yml"
    run "cd #{latest_release} && touch silex.log && chmod 777 silex.log"
    run "cd #{latest_release} && mkdir cache && chmod 777 cache"
  end
end
