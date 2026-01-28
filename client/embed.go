package client

import (
	"embed"
	"io/fs"
)

//go:embed dist/*
var Dist embed.FS

func GetDistFS() (fs.FS, error) {
	return fs.Sub(Dist, "dist")
}
