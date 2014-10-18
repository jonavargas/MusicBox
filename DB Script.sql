-- Database: music_box --

-- DROP DATABASE music_box;
 
CREATE DATABASE music_box;


-- Table: audio_file --


CREATE TABLE audio_file
(
 id serial NOT NULL,
 file character varying(2000),
 parts integer,
 time_per_chunk character varying(200),
 CONSTRAINT pk_audio_file PRIMARY KEY (id)
);

-- Table: worker --


CREATE TABLE worker
(
 id serial NOT NULL,
 file_path_split character varying(2000),
 audio_file_id serial,
 CONSTRAINT pk_queue PRIMARY KEY (id),
 constraint fk_queue_file foreign key (audio_file_id) references audio_file (id)
);
 
-- Drop Tables --

-- DROP TABLE worker;

-- DROP TABLE audio_file;


 