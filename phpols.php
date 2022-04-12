<?php

class phpOLS
{

    private $binaryPointer = null;

    public $Damos = [];

    private $mapPack = null; //current Mappack
    private $Map = null; //current Map

    function __construct( $file, $DamosFile = null )
    {
        if ( !file_exists( $file ) ) {
            throw new Exception( "File does not exists" );
        }

        if ( is_null( $DamosFile ) || !file_exists( $DamosFile ) ) {
            throw new Exception( "Damos file not exists!" );
        }

        $this->binaryPointer = fopen( $file, "r+b" );
        if ( !$this->binaryPointer ) {
            throw new Exception( 'File open failed.' );
        }

        $fp = fopen( $DamosFile, "r" );
        $damos = $columns = [];
        while ( !feof( $fp ) ) {

            $line = fgetcsv( $fp, 2000, ";", '"' );
            if ( empty( $line ) ) {
                break;
            }

            if ( empty( $columns ) ) {
                $columns = $line;
                continue;
            }

            $line[24] = substr( $line[24], 1 );
            $line[37] = substr( $line[37], 1 );
            $line[53] = substr( $line[53], 1 );
            $damos[$line[1]] = array_combine( $columns, $line );
        }
        fclose( $fp );

        if ( empty( $damos ) ) {
            throw new Exception( "Damos are not correct" );
        }

        $this->Damos = $damos;
    }

    function Change( $row, $col, $value )
    {
        $RealValue = $value / $this->mapPack["Fieldvalues.Factor"];

        //seek to the offset value
        fseek( $this->binaryPointer, $this->Map["Fieldvalues"][$row][$col]["o"] );
        //pack value
        fwrite( $this->binaryPointer, pack( $this->Map["Fieldvalues"][$row][$col]["p"], $RealValue ) );


        $this->Map["Fieldvalues"][$row][$col]["v"] = $value;

        return $this;
    }


    function getMapById( $idMap )
    {
        if ( !isset( $this->Damos[$idMap] ) ) {
            throw new Exception( "ID map does not exists in Damos" );
        }

        $this->mapPack = $this->Damos[$idMap];

        $map = $axisKeys = [];

        if ( $this->mapPack["Rows"] > 1 && $this->mapPack["Columns"] > 1 )
            $axisKeys = ["AxisX", "AxisY"];
        elseif ( $this->mapPack["Columns"] > 1 && $this->mapPack["Rows"] == 1 )
            $axisKeys = ["AxisX"];

        $map["Fieldvalues"] = $this->buildMap( $this->mapPack["Fieldvalues.StartAddr"], $this->mapPack["DataOrg"], $this->mapPack["Columns"] * $this->mapPack["Rows"], $this->mapPack["Radix"], $this->mapPack["bSigned"], $this->mapPack["Precision"], $this->mapPack["Fieldvalues.Factor"] );
        $map["Fieldvalues"] = array_chunk( $map["Fieldvalues"], $this->mapPack["Columns"] );

        foreach ( $axisKeys as $axisKey ) {
            $totalValues = ( $axisKey == 'AxisX' ) ? $this->mapPack["Columns"]:
            $this->mapPack["Rows"];

            if ( $this->mapPack["$axisKey.DataSrc"] == 'eRom' )
                $map[$axisKey] = $this->buildMap( $this->mapPack["$axisKey.DataAddr"], $this->mapPack["$axisKey.DataOrg"], $totalValues, $this->mapPack["$axisKey.Radix"], $this->mapPack["$axisKey.bSigned"], $this->mapPack["$axisKey.Precision"], $this->mapPack["$axisKey.Factor"] );
            else
                $map[$axisKey] = range( 1, $totalValues );

        }

        $this->Map = $map;
        return $this;
    }

    function display()
    {
        if ( isset( $this->Map["AxisX"] ) ) {
            echo "AxisX Name: {$this->mapPack["AxisX.Name"]}\n";
        }

        if ( isset( $this->Map["AxisY"] ) ) {
            echo "AxisY Name: {$this->mapPack["AxisY.Name"]}\n";
        }

        echo "\n";

        if ( isset( $this->Map["AxisX"] ) ) {
            foreach ( $this->Map["AxisX"] as $k => $value ) {
                if ( $k == 0 )
                    printf( "%17s |", $value["v"] );
                else
                    printf( "%8s |", $value["v"] );
            }
            echo "\n";
        }

        foreach ( $this->Map["Fieldvalues"] as $kr => $values ) {

            if ( isset( $this->Map["AxisY"] ) ) {
                printf( "%8s|", $this->Map["AxisY"][$kr]["v"] );
            }
            foreach ( $values as $k => $value ) {
                printf( "%8s |", $value["v"] );
            }
            echo "\n";
        }
        echo "\n\n";
    }


    function buildMap( $startAddress, $DataOrg, $totalValues, $Radix = 10, $bSigned = 1, $Precision = 1, $Factor = 1 )
    {
        //fix broken precisions
        if ( $Precision < 0 ) {
            $Precision = 0;
        }

        switch ( $DataOrg ) {
            case 'eLoHi':
                $bit = 16;
                $packCode = 'v*';
                break;

            case 'eLoHiLoHi':
                $bit = 32;
                $packCode = 'V*';
                break;

            case 'eHiLoHiLo':
                $bit = 32;
                $packCode = 'N*';
                break;

            case 'eHiLo':
                $bit = 16;
                $packCode = 'n*';
                break;

            case 'eByte':
                $bit = 8;
                $packCode = 'C';
                break;
        }

        $startAddr = hexdec( $startAddress );
        fseek( $this->binaryPointer, $startAddr );
        $mapData = fread( $this->binaryPointer, $totalValues * $bit / 8 );

        if ( $Radix == 10 ) {
            $map = array_values( unpack( $packCode, $mapData ) );

            foreach ( $map as $k => $value ) {

                if ( $bSigned == 1 && $value >= pow( 2, $bit - 1 ) ) {
                    $value -= pow( 2, $bit );
                }

                $map[$k] = ["v" => sprintf( "%.{$Precision}f", $value * $Factor ), "o" => $startAddr + ( $k * $bit / 8 ), "b" => $bit, "p" => $packCode];
            }

        } else {
            $map = unpack( 'H*', $mapData );
            $map = str_split( $map[1], $bit / 4 );
        }

        return $map;
    }
}
