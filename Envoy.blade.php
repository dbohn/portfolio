@servers(['web' => ['rancher@cancrisoft.net']])

@task('deploy', ['on' => 'web'])
    docker pull dbohn/portfolio:latest
    docker stop portfolio
    docker rm portfolio
    docker run --name portfolio --restart always -e VIRTUAL_HOST=david-bohn.de -d dbohn/portfolio:latest
@endtask
