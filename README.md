# Weather application
This application collecting data from few sources and get an average
value in client scale

# How to start service
you just need to run docker-compose

    docker-compose up -d
    
# Mock data

service using generated data for last 20 days from February 24th, if you want to generate 
new one you just need to run

    php generate_data.php
    
this script generate 3 files in csv, json and xml formats and put it in `files` folder

# how to add new integration

Implement new `WeatherRepository` integration, 
then in `app/repositories.php` add one more repository in containerBuilder. That's enough to start using
one more integration

# how-to add new scale

open `src/Domain/Weather/Scale.php`, add new constants and implement convertion from all scales to new one and vice-versa
