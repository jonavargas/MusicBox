<?php

class Upload extends Eloquent
{
	protected $table = 'audio_file';
	protected $fillable = array('file', 'parts', 'time_per_chunk');
	protected $guarded  = array('id');
	public    $timestamps = false;
}
