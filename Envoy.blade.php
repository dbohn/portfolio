@servers(['web' => ['rancher@cancrisoft.net']])

@task('deploy', ['on' => 'web'])
    docker pull dbohn/portfolio:latest
    docker stop portfolio
    docker rm portfolio
    docker run --name portfolio --restart always -d -p 80:80 dbohn/portfolio:latest
@endtask
