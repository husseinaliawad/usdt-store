part of '../main.dart';

class ApiClient {
  String? token;

  Future<Map<String, dynamic>> post(
    String path,
    Map<String, dynamic> body,
  ) async {
    final res = await http
        .post(
          Uri.parse('$apiBaseUrl$path'),
          headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            if (token != null) 'Authorization': 'Bearer $token',
          },
          body: jsonEncode(body),
        )
        .timeout(const Duration(seconds: 45));
    final data =
        jsonDecode(res.body.isEmpty ? '{}' : res.body) as Map<String, dynamic>;
    if (res.statusCode < 200 || res.statusCode >= 300) {
      throw Exception(data['message'] ?? 'Request failed');
    }
    return data;
  }

  Future<Map<String, dynamic>> get(String path) async {
    final res = await http.get(
      Uri.parse('$apiBaseUrl$path'),
      headers: {
        'Accept': 'application/json',
        if (token != null) 'Authorization': 'Bearer $token',
      },
    );
    final data =
        jsonDecode(res.body.isEmpty ? '{}' : res.body) as Map<String, dynamic>;
    if (res.statusCode < 200 || res.statusCode >= 300) {
      throw Exception(data['message'] ?? 'Request failed');
    }
    return data;
  }

  Future<Map<String, dynamic>> multipart(
    String path, {
    required Map<String, String> fields,
    required Map<String, PlatformFile> files,
  }) async {
    final req = http.MultipartRequest('POST', Uri.parse('$apiBaseUrl$path'));
    req.headers['Accept'] = 'application/json';
    if (token != null) req.headers['Authorization'] = 'Bearer $token';
    req.fields.addAll(fields);

    for (final entry in files.entries) {
      final file = entry.value;
      if (file.bytes != null) {
        req.files.add(
          http.MultipartFile.fromBytes(
            entry.key,
            file.bytes!,
            filename: file.name,
          ),
        );
      } else if (file.path != null) {
        req.files.add(await http.MultipartFile.fromPath(entry.key, file.path!));
      }
    }

    final streamed = await req.send().timeout(const Duration(seconds: 60));
    final res = await http.Response.fromStream(streamed);
    final data =
        jsonDecode(res.body.isEmpty ? '{}' : res.body) as Map<String, dynamic>;
    if (res.statusCode < 200 || res.statusCode >= 300) {
      throw Exception(data['message'] ?? 'Request failed');
    }
    return data;
  }
}
