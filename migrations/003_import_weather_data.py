#!/usr/bin/env python

import os

script_dir = os.path.dirname(os.path.realpath(__file__))

cities = {
    1: "Budapest",
    2: "Debrecen",
    3: "Keszthely",
    4: "Miskolc",
    5: "Nyiregyhaza",
    6: "Pecs",
    7: "Sopron",
    8: "Szeged",
    9: "Szombathely",
    10: "Turkeve",
}

def weather_csv_url(data_type, city_name):
    dir = "maximum_temperature"
    if data_type == "tn":
        dir = "minimum_temperature"
    elif data_type == "r":
        dir = "precipitation_sum"

    return f"https://odp.met.hu/climate/station_data_series/daily/from_1901/{dir}/{data_type}_o_{city_name}_19012023.csv.zip" 

def tempfile_zip_name(data_type, city_name):
    global script_dir
    return f"/{script_dir}/{data_type}_o_{city_name}_19012023.csv.zip"

def download(url, output_file):
   os.system(f"curl {url} --output {output_file}") 

def mysql_import(**kwargs):
    template = """
        load data local infile ':filename'
        into table weather.:table
        fields terminated by ';'
        lines terminated by 'EOR\\n'
        ignore 1 lines
        (date, :data_col)
        set city_id = :city_id
    """
    for k, v in kwargs.items():
        template = template.replace(f":{k}", str(v))

    return os.system(f"mysql --user=root --password=root --local_infile=1 <<< \"{template}\"")

def csv_filename(data_type, city_name):
    global script_dir
    return f"{script_dir}/{data_type}_o_{city_name}_19012023.csv"

db_settings = {
    "tx": { "table": "maximum_temperatures", "data_col": "max_temp" },
    "tn": { "table": "minimum_temperatures", "data_col": "min_temp" },
    "r": { "table": "precipitation", "data_col": "precipitation" },
}

if __name__ == "__main__":
    for city_id, city_name in cities.items():
        for data_type in ["tx", "tn", "r"]:
            filename = csv_filename(data_type, city_name)
            errno = mysql_import(
                filename=filename,
                table=db_settings[data_type]["table"],
                data_col=db_settings[data_type]["data_col"],
                city_id=city_id,
            )
            if errno != 0:
                print(f"[ERROR] {filename} adatok importálása sikertelen")
            else:

                print(f"[INFO] {filename} adatok importálása sikeres")

            # csv_url = weather_csv_url(data_type, city_name)
            # tempfile_zip = tempfile_zip_name(data_type, city_name)
            #
            # download(csv_url, tempfile_zip)
            # shutil.unpack_archive(tempfile_zip, script_dir)

