{ pkgs ? import <nixpkgs> {} }:

pkgs.mkShell {
  buildInputs = [
    pkgs.php84
    pkgs.php84Packages.composer
  ];
}