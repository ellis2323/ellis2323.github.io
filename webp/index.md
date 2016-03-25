---
layout: page
title: Game Assets with WebP
tagline: WebP
tags : [GameDev]
---

## Introduction

I'm the CTO of IOPixel, a game studio based in France. I'm working on [TipOff 2](http://www.iopixel.com), the sequel of [TipOff](https://play.google.com/store/apps/details?id=com.iopixel.basketball2).
The first game size was consequent 20Mo but acceptable for many players. To limit the size of the APK, we use on Android the [multiple APKs](http://developer.android.com/google/play/publishing/multiple-apks.html) hack. It was painful to maintain! Seriously, i don't know the intern at Google who had this idea but what a bad realisation!!! 

Things to know:

 * APK is a compressed ZIP file.
 * APK limit size is 50Mo!
 * Multiple-APKs support only old compressed texture format (ETC1, ATC, DXT, PVRT) but no ETC2 & ASTC
 * Obb extension file is available but you have only 2 files for your product
 * There are people playing with your APKs, trying to install ATC APK on phones not supported! And you can spend many support hours to search why it crash on its device (Users will not tell you that he had download your APK on a warez site).

 So if you can't maintain APK < 50Mo, here comes the Challenge.

## TipOff 2 Case

Due to performance motives, we at IOPixel use many texture formats on Android Device. The numbers here are the size after zip compression:

 * uncompressed data with PNG files: 52Mo (zip ratio is very low 10%)
 * PVRT files: 13Mo (zip ratio is very high 66Mo otherwise)
 * ATC files: 16Mo (zip ratio is so high 132Mo otherwise)
 * DXT files 18Mo (zip ratio is so high 132Mo otherwise)
 * KTX files: 13Mo (zip ratio is crazy 132Mo otherwise)

 PNG are the backup format, used only when no compression format is accepted. DXT is for Tegra 3&4, because NVidia is not compatible OGLES 3.0!!! ATC is used
 by adreno devices. KTX (ETC1) files is for Mali 400 devices (there is one file for RGB + one file for Alpha). Some data are in PNG format only because there are GUI Images (not used when we need 60fps). 
 In future, i think to add ETC2 format.

Note to everybody not using Texture Compression: you should !!!

 * TipOff 2 on Galaxy S2 with PNGs 40fps on Forest, with KTX files 60fps
 * TipOff 2 on San Diego (Intel Atom Z2460) with PNGs on Forest 45fps, with PVR files 60 fps
 * TipOff 2 on many phones is 60 fps but with the economy of Bandwith your GPU use less power.
 * TipOff loading is 3 times faster with compressed files! 

 So we need at minimum PNG + One format Compressed for multiple APKs => Far More than 50Mo... Ok, i will use the obb system with one unique APK (we could optimize with multiple APKs but because support problem
 i don't want it).

 And now come the result: one APK of 22Mo (mp3s + few pngs + .so files) and one enormous obb file: 100Mo. 125Mo is what the user see when he want to download TipOff 2. This week we made 4 updates: 500Mo for our beta testers :) Thx guys, i love you.

## PNG alternatives for game assets

 There are many files format for images... During my life, i have used BMP, TGA, GIF, PNG, JPEG, JP2.... So for TipOff 2, i want an alternative to PNG:

 * support of Alpha Channel
 * Compression with and with few loss. I don't want to shrink my fonts
 * fast integration on OS X, iOS, Android, Linux and Windows

 After many research, i found some file formats:

 * JPeg2000 ([OpenJPEG](http://uclouvain.github.io/openjpeg/) and [JASPER](http://www.ece.uvic.ca/~frodo/jasper/))
 * JPegXR ([wikipedia](https://en.wikipedia.org/wiki/JPEG_XR))
 * WebP ([Website](https://developers.google.com/speed/webp/))
 * BGP ([Website](http://bellard.org/bpg/))

You could compare these file formats with this [comparator](https://xooyoozoo.github.io/yolo-octo-bugfixes/#77-bombay-street&jxr=t&bpg=s). These formats could be all acceptable for TipOff 2. I'm looking for a 1/10 ratio. To select one of these file formats: i search sucessful integrations on iOS and Android. Jpeg2000 is native on iOS but on [Android](https://github.com/taka-no-me/android-cmake)... JpegXR seems a serious project but nobody uses it. BGP with the routing protocol is painful to search. Fabrice is a genious but not a marketing one's :) And WebP is a google beta Product.
WebP is available on Android 4.0 (but it is really available with 4.2.1). The integration with NDK is easy (https://gist.github.com/markbeaton/3719812), WebP is available on Port (OS X) and i build a iOS framework in 5 minutes. So i choose WebP. Not sure it's the best choice but it's a good one. If i failed with WebP, i will try BGP because of Fabrice Bellard.  

## WebP Integration

### OS X

I use port as everybody. So:

    sudo port install webp

WebP is installed. I could try to compress some PNG:

	ellis> cwebp -q 80 animation_loading.png animation_loading.webp
	Saving file 'animation_loading.webp'
	File:      data/textures/animation_loading.png
	Dimension: 1024 x 1024 (with alpha)
	Output:    52928 bytes Y-U-V-All-PSNR 44.38 44.66 45.37   44.58 dB
	block count:  intra4: 1307
	              intra16: 2789  (-> 68.09%)
	              skipped block: 2594 (63.33%)
	bytes used:  header:            288  (0.5%)
	             mode-partition:   7609  (14.4%)
	             transparency:     3423 (99.0 dB)
	 Residuals bytes  |segment 1|segment 2|segment 3|segment 4|  total
	    macroblocks:  |       2%|       9%|      17%|      71%|    4096
	      quantizer:  |      27 |      27 |      23 |      17 |
	   filter level:  |       8 |       5 |      10 |       8 |
	Lossless-alpha compressed size: 3420 bytes
	  * Lossless features used: PALETTE
	  * Precision Bits: histogram=5 transform=4 cache=0
	  * Palette size:   255
	 ellis> ls -al animation_loading.png
	   -rw-r--r-- 1 ellis staff 557072 Jun 26 21:50 data/textures/animation_loading.png
	 ellis> ls -al animation_loading.webp
	   -rw-r--r-- 1 ellis staff 52928 Jul 11 14:11 animation_loading.webp

So with quality factor at 80, i have 1/10 ratio. Let's look how it compress. I use Chrome as viewer :) Ok it's great. You could tune this factor with your art director.

Integration in my project was so easy. WebP has a header of 16 bytes. I check i found the signature and decode the data into my internal uncompressed data. In my Irrlicht
project, it is easily integrated:

	bool CImageLoaderWebP::isALoadableFileExtension(const io::path& filename) const {
		return core::hasFileExtension ( filename, "webp" );
	}

	bool CImageLoaderWebP::isALoadableFileFormat(io::IReadFile* file) const {
		if (!file) {
			return false;
	    }
	    // load Header
	    SWebPHeader header;
	    s64 size = file->getSize();
	    file->seek(0);
		file->read((void *)&header, sizeof(SWebPHeader));
	    // assert
	    bool check = header.data[0] == 'R';
	    check |= header.data[1] == 'I';
	    check |= header.data[2] == 'F';
	    check |= header.data[3] == 'F';

	    check |= header.data[8] == 'W';
	    check |= header.data[9] == 'E';
	    check |= header.data[10] == 'B';
	    check |= header.data[11] == 'P';

	    // header checked
	    return check;
	}

    IImage* CImageLoaderWebP::loadImage(io::IReadFile* file) const {
		// load image into memory
	    s64 size = file->getSize();
	    file->seek(0);
	    u8* data = new u8[size];
		file->read((void *)data, size);
	    //
	    s32 width = 0;
	    s32 height = 0;
	    s32 res = WebPGetInfo(data, size, &width, &height);
	    if (res != 1) {
	        os::Printer::logError("Cannot read WebP file");
	        return nullptr;
	    }
	    uint8_t* data2 = WebPDecodeBGRA(data, size, &width, &height);
	    CImage* image = new CImage(ECF_A8R8G8B8, core::dimension2d<u32>(width, height), data2, true, true);
	    uint8_t* data2 = WebPDecodeBGRA(data, size, &width, &height);
	    CImage* image = new CImage(ECF_A8R8G8B8, core::dimension2d<u32>(width, height), data2, true, true);
	    return image;
	}

That's all and it works :) The last job for a complete integration is to modify the pipeline workflow. 30 minutes to create a little script and modify my [doit](http://pydoit.org/) script.

### Android

Probably the most difficult integration. I have carefully added needed files to decode WebP and added them to my Android.mk:

	webp/dec/alpha.c \
	webp/dec/buffer.c \
	webp/dec/frame.c \
	webp/dec/idec.c \
	webp/dec/io.c \
	webp/dec/quant.c \
	webp/dec/tree.c \
	webp/dec/vp8.c \
	webp/dec/vp8l.c \
	webp/dec/webp.c \
	webp/dsp/alpha_processing.c \
	webp/dsp/alpha_processing_sse2.c \
	webp/dsp/cpu.c \
	webp/dsp/dec_clip_tables.c \
	webp/dsp/dec.c \
	webp/dsp/dec_sse2.c \
	webp/dsp/dec_neon.c \
	webp/dsp/dec_mips32.c \
	webp/dsp/lossless.c \
	webp/dsp/lossless_sse2.c \
	webp/dsp/lossless_neon.c \
	webp/dsp/lossless_mips32.c \
	webp/dsp/upsampling.c \
	webp/dsp/upsampling_neon.c \
	webp/dsp/upsampling_sse2.c \
	webp/dsp/yuv.c \
	webp/dsp/yuv_sse2.c \
	webp/dsp/yuv_mips32.c \
	webp/utils/bit_reader.c \
	webp/utils/bit_writer.c \
	webp/utils/color_cache.c \
	webp/utils/filters.c \
	webp/utils/huffman_encode.c \
	webp/utils/huffman.c \
	webp/utils/quant_levels.c \
	webp/utils/quant_levels_dec.c \
	webp/utils/random.c \
	webp/utils/rescaler.c \
	webp/utils/thread.c \
	webp/utils/utils.c


### iOS

That's so eaysy. Open terminal, goto into libwebp-0.4.3 and do:

     sh iosbuild.sh

5 minutes after, you have a WebP.framework ready to use in your project


### Results



