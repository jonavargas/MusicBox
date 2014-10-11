-- Database: music_box --

-- DROP DATABASE music_box;
 
CREATE DATABASE music_box;


-- Table: file_mp3 --


CREATE TABLE file_mp3
(
 id serial NOT NULL,
 file character varying(100),
 parts integer,
 time_per_chunk time without time zone,
 CONSTRAINT pk_file_mp3 PRIMARY KEY (id)
);

-- Table: queue --


CREATE TABLE queue
(
 id serial NOT NULL,
 file_path_split character varying(200),
 message_json character varying(200),
 file_mp3_id serial,
 CONSTRAINT pk_queue PRIMARY KEY (id),
 constraint fk_queue_file foreign key (file_mp3_id) references file_mp3 (id)
);
 
-- Drop Tables --

-- DROP TABLE queue;

-- DROP TABLE file_mp3;


 