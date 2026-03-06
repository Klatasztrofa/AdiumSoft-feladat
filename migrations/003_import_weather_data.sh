#!/bin/bash

# Csak containeren belül fog működni így, de végülis az a lényeg
python3 "/docker-entrypoint-initdb.d/003_import_weather_data.py"
