<?php


namespace Digitalkreativ\Utilities;


class FileUtils
{

    public static function content( $absPathFileName )
    {
        if( file_exists( $absPathFileName ) ){
            if( is_file( $absPathFileName ) ){
                return file_get_contents( $absPathFileName );
            }
        }

        throw new \Exception('File not found');
    }

    public static function save( $absPathFileName, $content )
    {
        return file_put_contents( $absPathFileName, $content, LOCK_EX );
    }

    public static function delete( $absPathFileName )
    {
        if( file_exists( $absPathFileName ) ){
            if( is_file( $absPathFileName ) ){
                return unlink( $absPathFileName );
            }
        }

        throw new \Exception('File not found');
    }

    public static function mimeType( $absPathFileName )
    {
        $mimeType = '';

        if( file_exists( $absPathFileName ) ){
            if( is_file( $absPathFileName ) ){
                $fileInfo = finfo_open( FILEINFO_MIME_TYPE ); // MIME types: http://filext.com/faq/office_mime_types.php
                $mimeType = finfo_file( $fileInfo, $absPathFileName );
                finfo_close( $fileInfo );
            }
        }


        return $mimeType;
    }

    public static function mimeTypeToExtension( $mimeType )
    {
        $extension = '';

        switch ( $mimeType ) {
            case 'image/jpeg':
            case 'image/jpg':
            case 'image/jpe':
                $extension = 'jpg'; break;

            case 'image/png':
                $extension = 'png'; break;

            case 'image/gif':
                $extension = 'gif'; break;

            case 'application/pdf':
                $extension = 'pdf'; break;

            case 'application/x-zip-compressed':
                $extension = 'zip'; break;

            case 'application/vnd.ms-excel':
                $extension = 'xls'; break;

            case 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet':
                $extension = 'xlsx'; break;

            case 'application/msword':
            case 'application/application/msword':
                $extension = 'doc'; break;

            case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
                $extension = 'docx'; break;

            case 'application/vnd.ms-powerpoint':
                $extension = 'ppt'; break;

            default:
                break;
        }

        return $extension;

    }
}