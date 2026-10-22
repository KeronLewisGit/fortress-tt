"""Splits tall desktop screenshots into letter-proportioned chunks so each PDF page shows a readable slice."""
import json, os, sys
from PIL import Image

SHOTS = 'dist/proposal/shots'
RATIO = 1.08  # chunk height / width – fits the image area of a letter page with heading + caption
man = json.load(open(f'{SHOTS}/manifest.json'))
out = {}
for shot in man['desktop']:
    im = Image.open(shot['file']).convert('RGB')
    w, h = im.size
    chunk_h = int(w * RATIO)
    chunks = []
    if h <= chunk_h * 1.08:
        chunks.append(shot['file'])
    else:
        n = 0
        y = 0
        while y < h:
            box = (0, y, w, min(y + chunk_h, h))
            # avoid a tiny trailing sliver: merge into previous chunk if < 18% of a chunk
            if h - y < chunk_h * 0.18 and chunks:
                break
            part = im.crop(box)
            f = shot['file'].replace('.jpg', f'-{n}.jpg')
            part.save(f, 'JPEG', quality=84, optimize=True)
            chunks.append(f)
            n += 1
            y += chunk_h
    out[shot['name']] = {'url': shot['url'], 'chunks': [os.path.abspath(c) for c in chunks], 'w': w, 'h': h}
for shot in man['mobile']:
    out[shot['name']] = {'url': shot['url'], 'chunks': [os.path.abspath(shot['file'])], 'mobile': True}
json.dump(out, open(f'{SHOTS}/chunks.json', 'w'), indent=1)
print({k: len(v['chunks']) for k, v in out.items()})
